<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScraperControllerOptimized extends Controller
{
    public function scrape(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid website URL'
            ], 422);
        }

        try {
            $client = new Client(['timeout' => 30]);
            $html = $client->get($request->website)->getBody()->getContents();

            // Extract emails and phones
            preg_match_all('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $html, $emailMatches);
            preg_match_all('/(?:\+?1[-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $html, $phoneMatches);

            $emails = array_unique(array_filter($emailMatches[0], fn($email) => 
                filter_var($email, FILTER_VALIDATE_EMAIL) && 
                !preg_match('/(noreply|no-reply|support@example)/i', $email)
            ));

            $phones = array_unique(array_filter($phoneMatches[0], fn($phone) => 
                strlen(preg_replace('/[^0-9]/', '', $phone)) >= 10
            ));

            // Extract company name
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

            // Auto-create leads if requested
            if ($request->auto_create_leads && count($emails) > 0) {
                foreach ($emails as $email) {
                    Lead::firstOrCreate(['email' => $email], [
                        'company' => $companyName,
                        'website' => $request->website,
                        'phone' => $phones[0] ?? null
                    ]);
                }
                $results['leads_created'] = true;
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Scraping failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to scrape website'
            ], 500);
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
                'message' => 'Invalid websites data'
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

    private function extractCompanyName($html, $domain)
    {
        // Try title tag
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            $title = trim($matches[1]);
            $title = preg_replace('/\s*[-|–].*$/', '', $title);
            if (strlen($title) > 0 && strlen($title) < 100) {
                return $title;
            }
        }

        // Fallback to domain
        $domain = str_replace('www.', '', $domain);
        return ucfirst(str_replace(['.com', '.org', '.net', '.co'], '', $domain));
    }
}