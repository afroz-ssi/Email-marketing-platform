<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Models\OutlookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class CampaignControllerOptimized extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::query()
            ->when($request->name, fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->message, fn($q) => $q->where('message', 'like', '%' . $request->message . '%'))
            ->latest()
            ->paginate($request->perPage ?? 20)
            ->withQueryString();

        return Inertia::render('Admin/campaigns/List', compact('campaigns'));
    }

    public function createCampaign(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'message' => 'required|string'
            ]);

            Campaign::create($request->only('name', 'message'));
            return redirect()->route('admin.campaigns')->with('success', 'Campaign created successfully.');
        }

        return Inertia::render('Admin/campaigns/CreateEdit');
    }

    public function send(Campaign $campaign)
    {
        $leads = Lead::withEmail()->get();
        $sentCount = 0;

        foreach ($leads as $lead) {
            // Skip if already sent
            if (EmailLog::where(['campaign_id' => $campaign->id, 'lead_id' => $lead->id])->exists()) {
                continue;
            }

            $account = OutlookAccount::available()->first();
            if (!$account) break;

            try {
                Mail::raw($campaign->message, function ($mail) use ($lead, $account, $campaign) {
                    $mail->from($account->email)->to($lead->email)->subject($campaign->name);
                });

                EmailLog::create([
                    'campaign_id' => $campaign->id,
                    'lead_id' => $lead->id,
                    'outlook_email' => $account->email,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $account->increment('sent_today');
                if ($account->sent_today >= $account->daily_limit) {
                    $account->update(['status' => 'sent']);
                }
                $sentCount++;
            } catch (\Exception $e) {
                Log::error("Email failed: " . $e->getMessage());
            }
        }

        $campaign->increment('sent_count', $sentCount);
        return redirect()->route('admin.campaigns')->with('success', "Campaign sent! {$sentCount} emails delivered.");
    }

    public function editCampaign(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $campaign->update($request->only('name', 'message'));
            return redirect()->route('admin.campaigns')->with('success', 'Campaign updated successfully');
        }

        return Inertia::render('Admin/campaigns/CreateEdit', compact('campaign'));
    }

    public function deleteCampaign($id)
    {
        Campaign::findOrFail($id)->delete();
        return back()->with('success', 'Campaign deleted successfully');
    }

    public function resetAllAccounts()
    {
        OutlookAccount::query()->update(['sent_today' => 0, 'status' => 'queued']);
        return back()->with('success', 'All accounts reset successfully');
    }

    public function analytics()
    {
        $data = [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::where('status', 'active')->count(),
            'total_emails_sent' => EmailLog::count(),
            'emails_sent_today' => EmailLog::whereDate('sent_at', today())->count(),
            'top_campaigns' => Campaign::orderBy('sent_count', 'desc')->limit(10)->get(),
            'recent_activity' => EmailLog::with(['campaign', 'lead'])->latest('sent_at')->limit(20)->get(),
            'daily_stats' => EmailLog::selectRaw('DATE(sent_at) as date, COUNT(*) as count')
                ->whereNotNull('sent_at')
                ->whereBetween('sent_at', [now()->subDays(30), now()])
                ->groupBy('date')
                ->get(),
            'outlook_performance' => OutlookAccount::select('email', 'sent_today', 'daily_limit')->get()
        ];

        return Inertia::render('Admin/campaigns/Analytics', compact('data'));
    }
}