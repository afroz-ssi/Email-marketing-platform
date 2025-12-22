<?php

namespace App\Console\Commands;

use App\Models\OutlookAccount;
use Illuminate\Console\Command;

class ResetDailyCounts extends Command
{
    protected $signature = 'outlook:reset-daily-counts';
    protected $description = 'Reset daily sent counts for all Outlook accounts';

    public function handle()
    {
        OutlookAccount::query()->update([
            'sent_today' => 0,
            'status' => 'queued'
        ]);

        $this->info('Daily counts reset successfully for all accounts.');
    }
}
