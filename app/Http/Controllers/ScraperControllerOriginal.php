<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScraperController extends Controller
{
    public function scrape(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid website URL',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = new Client([
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $response = $client->get($request->website);
            $html = $response->getBody()->getContents();

            // Extract emails with better regex
            preg_match_all('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $html, $emailMatches);
            $emails = array_unique(array_filter($emailMatches[0], function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL) && 
                       !preg_match('/(noreply|no-reply|donotreply|support|info@example)/i', $email);
            }));

            // Extract phone numbers with better regex
            preg_match_all('/(?:\+?1[-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $html, $phoneMatches);
            $phones = array_unique(array_filter($phoneMatches[0], function($phone) {
                $cleaned = preg_replace('/[^0-9]/', '', $phone);
                return strlen($cleaned) >= 10;
            }));

            // Extract company name from domain or title
            $domain = parse_url($request->website, PHP_URL_HOST);
            $companyName = $this->extractCompanyName($html, $domain);

            $results = [
                'success' => true,
                'website' => $request->website,
                'company' => $companyName,
                'emails' => array_values($emails),
                'phones' => array_values($phones),
                'total_found' => count($emails) + count($phones)
            ];

            // Optionally auto-create leads
            if ($request->auto_create_leads && (count($emails) > 0 || count($phones) > 0)) {
                $this->createLeadsFromScrapedData($results);
                $results['leads_created'] = true;
            }

            return response()->json($results);

        } catch (RequestException $e) {
            Log::error('Scraping failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to scrape website. Please check the URL and try again.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Scraping error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while scraping.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function extractCompanyName($html, $domain)
    {
        // Try to extract from title tag
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            $title = trim($matches[1]);
            $title = preg_replace('/\s*[-|–].*$/', '', $title); // Remove taglines
            if (strlen($title) > 0 && strlen($title) < 100) {
                return $title;
            }
        }

        // Fallback to domain name
        $domain = str_replace('www.', '', $domain);
        return ucfirst(str_replace(['.com', '.org', '.net', '.co'], '', $domain));
    }

    private function createLeadsFromScrapedData($data)
    {
        foreach ($data['emails'] as $email) {
            Lead::firstOrCreate(
                ['email' => $email],
                [
                    'company' => $data['company'],
                    'website' => $data['website'],
                    'phone' => $data['phones'][0] ?? null
                ]
            );
        }
    }

    public function bulkScrape(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'websites' => 'required|array|max:10',
            'websites.*' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid websites data',
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        foreach ($request->websites as $website) {
            $scrapeRequest = new Request(['website' => $website, 'auto_create_leads' => true]);
            $result = $this->scrape($scrapeRequest);
            $results[] = json_decode($result->getContent(), true);
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total_websites' => count($request->websites)
        ]);
    }
}
