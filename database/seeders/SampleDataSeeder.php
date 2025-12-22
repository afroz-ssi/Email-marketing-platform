<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\OutlookAccount;
use App\Models\EmailLog;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample leads
        $leads = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+1-555-0101', 'company' => 'Tech Corp', 'website' => 'https://techcorp.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@business.com', 'phone' => '+1-555-0102', 'company' => 'Business Inc', 'website' => 'https://business.com'],
            ['name' => 'Mike Johnson', 'email' => 'mike@startup.io', 'phone' => '+1-555-0103', 'company' => 'Startup IO', 'website' => 'https://startup.io'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@agency.net', 'phone' => '+1-555-0104', 'company' => 'Creative Agency', 'website' => 'https://agency.net'],
            ['name' => 'David Brown', 'email' => 'david@consulting.org', 'phone' => '+1-555-0105', 'company' => 'Brown Consulting', 'website' => 'https://consulting.org'],
        ];

        foreach ($leads as $leadData) {
            Lead::create($leadData);
        }

        // Create sample Outlook accounts
        $outlookAccounts = [
            ['email' => 'marketing1@outlook.com', 'daily_limit' => 100, 'sent_today' => 25, 'status' => 'queued'],
            ['email' => 'marketing2@outlook.com', 'daily_limit' => 150, 'sent_today' => 100, 'status' => 'queued'],
            ['email' => 'marketing3@outlook.com', 'daily_limit' => 200, 'sent_today' => 200, 'status' => 'sent'],
        ];

        foreach ($outlookAccounts as $accountData) {
            OutlookAccount::create($accountData);
        }

        // Create sample campaigns
        $campaigns = [
            [
                'name' => 'Welcome Campaign',
                'message' => 'Welcome to our platform! We are excited to have you on board.',
                'sent_count' => 15,
                'status' => 'active'
            ],
            [
                'name' => 'Product Launch',
                'message' => 'Check out our new product launch! Limited time offer available.',
                'sent_count' => 8,
                'status' => 'active'
            ],
            [
                'name' => 'Newsletter Q4',
                'message' => 'Our quarterly newsletter with updates and insights.',
                'sent_count' => 0,
                'status' => 'draft'
            ],
        ];

        foreach ($campaigns as $campaignData) {
            Campaign::create($campaignData);
        }

        // Create sample email logs
        $emailLogs = [
            ['campaign_id' => 1, 'lead_id' => 1, 'outlook_email' => 'marketing1@outlook.com', 'status' => 'sent', 'sent_at' => now()->subDays(2)],
            ['campaign_id' => 1, 'lead_id' => 2, 'outlook_email' => 'marketing1@outlook.com', 'status' => 'sent', 'sent_at' => now()->subDays(2)],
            ['campaign_id' => 1, 'lead_id' => 3, 'outlook_email' => 'marketing2@outlook.com', 'status' => 'sent', 'sent_at' => now()->subDays(1)],
            ['campaign_id' => 2, 'lead_id' => 1, 'outlook_email' => 'marketing2@outlook.com', 'status' => 'sent', 'sent_at' => now()->subHours(5)],
            ['campaign_id' => 2, 'lead_id' => 4, 'outlook_email' => 'marketing1@outlook.com', 'status' => 'sent', 'sent_at' => now()->subHours(3)],
        ];

        foreach ($emailLogs as $logData) {
            EmailLog::create($logData);
        }

        echo "Sample data created successfully!\n";
        echo "- " . count($leads) . " leads created\n";
        echo "- " . count($outlookAccounts) . " Outlook accounts created\n";
        echo "- " . count($campaigns) . " campaigns created\n";
        echo "- " . count($emailLogs) . " email logs created\n";
    }
}