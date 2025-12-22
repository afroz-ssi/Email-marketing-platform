<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Models\OutlookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class CampaignController extends Controller
{
    // public function send(Campaign $campaign)
    // {
    //     $leads = Lead::whereNotNull('email')->get();

    //     foreach ($leads as $lead) {
    //         $account = $this->getAvailableAccount();
    //         if (!$account) break;

    //         Mail::raw($campaign->message, function ($mail) use ($lead, $account) {
    //             $mail->from($account->email)
    //                 ->to($lead->email)
    //                 ->subject('Titan Outreach');
    //         });

    //         EmailLog::create([
    //             'campaign_id' => $campaign->id,
    //             'lead_id' => $lead->id,
    //             'outlook_email' => $account->email,
    //             'sent_at' => now(),
    //         ]);

    //         $account->increment('sent_today');
    //         $campaign->increment('sent_count');
    //     }
    // }

    // public function analytics()
    // {
    //     return [
    //         'total_sent' => EmailLog::count(),
    //         'by_campaign' => Campaign::select('name', 'sent_count')->get(),
    //     ];
    // }


    public function index(Request $request)
    {
        try {

            $campaigns = Campaign::query();

            if ($request->name) {
                $campaigns = $campaigns->where('name', 'like', '%' . trim($request->name) . '%');
            }

            if ($request->message) {
                $campaigns = $campaigns->where('message', 'like', '%' . trim($request->message) . '%');
            }

            if ($request->fieldName && $request->shortBy) {
                $campaigns->orderBy($request->fieldName, $request->shortBy);
            }

            $perPage = $request->perPage ?? 20;
            $campaigns = $campaigns->latest()->paginate($perPage)->withQueryString();
            return Inertia::render('Admin/campaigns/List', compact('campaigns'));
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION CAMPAIGNS LIST :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            abort(500);
        }
    }


    public function createCampaign(Request $request)
    {
        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                'name' => 'required',
                'message' => 'required'
            ]);

            Campaign::create($validatedData);

            return redirect()->route('admin.campaigns')->with('success', 'Campaign created successfully.');
        }
        return Inertia::render('Admin/campaigns/CreateEdit');
    }

    public function send(Campaign $campaign)
    {
        $leads = Lead::withEmail()->get();
        $sentCount = 0;
        $errors = [];

        foreach ($leads as $lead) {
            // Check if already sent to this lead for this campaign
            $alreadySent = EmailLog::where('campaign_id', $campaign->id)
                                 ->where('lead_id', $lead->id)
                                 ->exists();
            
            if ($alreadySent) {
                continue;
            }

            $account = OutlookAccount::available()->first();
            if (!$account) {
                $errors[] = 'No available Outlook accounts with remaining daily limit';
                break;
            }

            try {
                Mail::raw($campaign->message, function ($mail) use ($lead, $account, $campaign) {
                    $mail->from($account->email)
                        ->to($lead->email)
                        ->subject($campaign->name . ' - Titan Outreach');
                });

                EmailLog::create([
                    'campaign_id' => $campaign->id,
                    'lead_id' => $lead->id,
                    'outlook_email' => $account->email,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $account->increment('sent_today');
                $sentCount++;

                // Check if account reached daily limit
                if ($account->sent_today >= $account->daily_limit) {
                    $account->update(['status' => 'sent']);
                }

            } catch (\Exception $e) {
                $errors[] = "Failed to send to {$lead->email}: " . $e->getMessage();
                Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        // Update campaign
        $campaign->increment('sent_count', $sentCount);
        if ($sentCount > 0) {
            $campaign->update(['status' => 'active']);
        }

        $message = "Campaign sent successfully. {$sentCount} emails sent.";
        if (!empty($errors)) {
            $message .= ' Some errors occurred: ' . implode(', ', array_slice($errors, 0, 3));
        }

        return redirect()->route('admin.campaigns')->with('success', $message);
    }

    public function editCampaign(Request $request, $id)
    {
        PermissionHelper::abort403('Campaigns');

        $campaign = Campaign::find($id);

        if (!$campaign) {
            return redirect()->back()->with('error', 'Campaign not found');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|max:255|string',
                'message' => 'required|string',
            ]);

            try {
                $campaign->update([
                    'name' => $request->name,
                    'message' => $request->message,
                ]);

                session()->flash('success', 'Campaign successfully updated');
                return redirect()->route('admin.campaigns');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                return redirect()->back()->with('error', 'An error occurred while updating the campaign');
            }
        }

        return Inertia::render('Admin/campaigns/CreateEdit', compact('campaign'));
    }

    public function deleteCampaign($id)
    {
        PermissionHelper::abort403('Campaigns');

        try {
            $campaign = Campaign::find($id);

            if (!$campaign) {
                return redirect()->back()->with('error', 'Campaign not found');
            }

            $campaign->delete();

            session()->flash('success', 'Campaign successfully deleted');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while deleting the campaign');
        }
    }

    public function resetAllAccounts()
    {
        PermissionHelper::abort403('Campaigns');

        try {
            OutlookAccount::query()->update([
                'sent_today' => 0,
                'status' => 'queued'
            ]);

            session()->flash('success', 'All Outlook accounts reset successfully');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while resetting accounts');
        }
    }

    public function analytics()
    {
        try {
            $data = [
                'total_campaigns' => Campaign::count(),
                'active_campaigns' => Campaign::where('status', 'active')->count(),
                'total_emails_sent' => EmailLog::count(),
                'emails_sent_today' => EmailLog::whereDate('sent_at', today())->count(),
                'top_campaigns' => Campaign::orderBy('sent_count', 'desc')->limit(10)->get(),
                'recent_activity' => EmailLog::with(['campaign', 'lead'])
                    ->latest('sent_at')
                    ->limit(20)
                    ->get(),
                'daily_stats' => EmailLog::selectRaw('DATE(sent_at) as date, COUNT(*) as count')
                    ->whereNotNull('sent_at')
                    ->whereBetween('sent_at', [now()->subDays(30), now()])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'outlook_performance' => OutlookAccount::select('email', 'sent_today', 'daily_limit')
                    ->get()
                    ->map(function ($account) {
                        return [
                            'email' => $account->email,
                            'sent_today' => $account->sent_today,
                            'daily_limit' => $account->daily_limit,
                            'usage_percentage' => $account->usage_percentage
                        ];
                    })
            ];

            return Inertia::render('Admin/campaigns/Analytics', compact('data'));
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION CAMPAIGN ANALYTICS :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            abort(500);
        }
    }
}
