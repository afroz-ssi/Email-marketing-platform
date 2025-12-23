<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\LoginVerificationMail;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Models\OutlookAccount;
use App\Models\User;
use App\Rules\MatchOldPassword;
use App\Rules\NewOldPasswordNotSame;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::user() && in_array(Auth::user()->role_name, ['SUPER-ADMIN', 'ADMIN'])) {
            return to_route('admin.dashboard');
        }

        return Inertia::render('common/SuperAdminLogin');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i'],
            'password' => ['required']
        ]);


        if (Auth::attempt($credentials)) {
            if (!in_array(Auth::user()->role_name, ['SUPER-ADMIN'])) {
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized role.'])->onlyInput('email');
            }
            if (Auth::user()->role_name == 'SUPER-ADMIN') {
                $request->session()->regenerate();
                return redirect()->intended('/admin/dashboard');
            }
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function dashboard()
    {
        $data = [
            'totalSent' => EmailLog::count(),
            'totalLeads' => Lead::count(),
            'totalCampaigns' => Campaign::count(),
            'totalOutlookAccounts' => OutlookAccount::count(),
            'activeOutlookAccounts' => OutlookAccount::active()->count(),
            'campaigns' => Campaign::select('name', 'sent_count')->orderBy('sent_count', 'desc')->limit(5)->get(),
            'outlookUsage' => OutlookAccount::select('email', 'daily_limit', 'sent_today')->get(),

            'monthlyEmails' => EmailLog::selectRaw('MONTHNAME(sent_at) as month, COUNT(*) as total')
                ->whereYear('sent_at', now()->year)
                ->whereNotNull('sent_at')
                ->groupBy('month')
                ->get(),
            'weeklyEmails' => EmailLog::selectRaw('DAYNAME(sent_at) as day, COUNT(*) as total')
                ->whereBetween('sent_at', [now()->subWeek(), now()])
                ->groupBy('day')
                ->get()
        ];
            
        return Inertia::render('Admin/Dashboard', compact('data'));
    }

    function adminProfile(Request $request)
    {
        $user = Auth::user();

        if (request()->isMethod('post')) {
            $credentials = $request->validate([
                'first_name' => 'required|max:40|alpha',
                'last_name' => 'required|max:40|alpha',
                'email' =>  'required|max:255|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,' . $user->id,
                'profile_photo' =>  $request->hasFile('profile_photo') ? 'mimes:jpeg,png,jpg,gif' : '',
            ]);

            try {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;

                if ($request->input('profile_photo')) {
                    $base64Image = $request->input('profile_photo');
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                    $filename = uniqid() . time() . rand(10, 1000000) . '.jpg';
                    $filepath = 'profile_photo/' . $filename;
                    Storage::disk('public')->put($filepath, $imageData);
                    $filepath = 'storage/profile_photo/' . $filename;
                    $user->profile_photo_path = $filepath;
                }
                $user->save();

                session()->flash('success', 'Profile updated successfully');
                return redirect('admin/dashboard');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                return response()->json(['error' => 'Server Error'], 500);
            }
        }

        return Inertia::render('Admin/AdminProfile', compact('user'));
    }

    public function adminChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'min:8', new NewOldPasswordNotSame($request->current_password)],
            'confirm_password' => ['required_with:new_password', 'same:new_password'],
        ]);

        $request->user()->password = $request->new_password;
        $request->user()->save();

        Auth::logout();
        session()->flash('success', 'Password changed successfully');
        return redirect('/admin/login');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
