<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\OutlookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OutlookAccountController extends Controller
{
    public function index(Request $request)
    {
        PermissionHelper::abort403('OutlookAccount');
        try {
            $accounts = OutlookAccount::query();
            
            if ($request->email) {
                $accounts = $accounts->where('email', 'like', '%' . trim($request->email) . '%');
            }
            
            if ($request->status) {
                $accounts = $accounts->where('status', $request->status);
            }
            
            if ($request->fieldName && $request->shortBy) {
                $accounts->orderBy($request->fieldName, $request->shortBy);
            }
            
            $perPage = $request->perPage ?? 20;
            $accounts = $accounts->latest()->paginate($perPage)->withQueryString();
            
            return Inertia::render('Admin/outlook/List', ['accounts' => $accounts]);
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            abort(500);
        }
    }

    public function create(Request $request)
    {
        PermissionHelper::abort403('OutlookAccount');

        if ($request->isMethod('post')) {
            $request->validate([
                'email' => ['required', 'email', 'max:255', Rule::unique('outlook_accounts')],
                'daily_limit' => 'required|integer|min:1|max:1000',
                'status' => 'required|in:queued,sent',
            ]);

            try {
                OutlookAccount::create([
                    'email' => $request->email,
                    'daily_limit' => $request->daily_limit,
                    'sent_today' => 0,
                    'status' => $request->status,
                ]);

                session()->flash('success', 'Outlook account successfully created');
                return redirect()->route('admin.outlookAccounts');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                abort(500);
            }
        }
        
        return Inertia::render('Admin/outlook/CreateEdit');
    }

    public function edit(Request $request, $id)
    {
        PermissionHelper::abort403('OutlookAccount');

        $account = OutlookAccount::find($id);

        if (!$account) {
            return redirect()->back()->with('error', 'Outlook account not found');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'email' => ['required', 'email', 'max:255', Rule::unique('outlook_accounts')->ignore($account->id)],
                'daily_limit' => 'required|integer|min:1|max:1000',
                'sent_today' => 'required|integer|min:0',
                'status' => 'required|in:queued,sent',
            ]);

            try {
                $account->update([
                    'email' => $request->email,
                    'daily_limit' => $request->daily_limit,
                    'sent_today' => $request->sent_today,
                    'status' => $request->status,
                ]);

                session()->flash('success', 'Outlook account successfully updated');
                return redirect()->route('admin.outlookAccounts');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                return redirect()->back()->with('error', 'An error occurred while updating the account');
            }
        }

        return Inertia::render('Admin/outlook/CreateEdit', compact('account'));
    }

    public function destroy($id)
    {
        PermissionHelper::abort403('OutlookAccount');

        try {
            $account = OutlookAccount::find($id);
            
            if (!$account) {
                return redirect()->back()->with('error', 'Outlook account not found');
            }
            
            $account->delete();

            session()->flash('success', 'Outlook account successfully deleted');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while deleting the account');
        }
    }

    public function resetSentCount($id)
    {
        PermissionHelper::abort403('OutlookAccount');

        try {
            $account = OutlookAccount::find($id);
            
            if (!$account) {
                return redirect()->back()->with('error', 'Outlook account not found');
            }
            
            $account->update(['sent_today' => 0, 'status' => 'queued']);

            session()->flash('success', 'Sent count reset successfully');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while resetting the count');
        }
    }
}