<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Mail\SendClientUpdateDetailsMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function userlist(Request $request)
    {
        PermissionHelper::abort403('User');
        try {
            $users = User::query();
            if ($request->name) {
                $users = $users->WhereRaw(
                    "concat(first_name,' ', last_name) like '%" . trim(addslashes($request->name)) . "%' "
                );
            }

            if ($request->email) {
                $users = $users->where('email', 'like', '%' . trim($request->email) . '%');
            }

            if ($request->phone) {
                $users = $users->where('phone', 'like', '%' . trim($request->phone) . '%');
            }
            if (isset($request->active)) {
                $users = $users->where('active', $request->active);
            }

            if ($request->fieldName && $request->shortBy) {
                $users->orderBy($request->fieldName, $request->shortBy);
            }

            $perPage = $request->perPage ?? 20;
            $users = $users->latest()->role('user')->paginate($perPage)->withQueryString();
            return Inertia::render('Admin/user/List', ['users' => $users]);
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            abort(500);
        }
    }

    public function createUser(Request $request)
    {
        PermissionHelper::abort403('User');

        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'first_name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
                'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:100', Rule::unique('users')],
                'password' => 'required|min:8|max:50',
                'phone' => ['required', 'digits_between:10,15', 'numeric', Rule::unique('users')],
                'dob' => 'required',
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB
                'status' => 'required',
            ],['profile_photo.max' => 'Profile photo must not be greater than 2MB']);

            try {
                $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->password = $request->password;
                $user->phone = $request->phone;
                $user->dob = date('Y-m-d', strtotime($request->dob));
                $user->active = $request->status ?? 1;
                if ($request->hasFile('profile_photo')) {
                    $file = $request->file('profile_photo');
                    $path = 'profile_photo';
                    $final_image_url = ImageHelper::customSaveImage($file, $path);
                    $user->profile_photo_path = $final_image_url;
                }
                $user->save();
                $user->assignRole('USER');

                session()->flash('success', 'Client successfully created');
                return redirect()->route('admin.users');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                abort(500);
            }
        }
        return Inertia::render('Admin/user/CreateEdit');
    }

    public function editUser(Request $request, $id)
    {
        PermissionHelper::abort403('User');

        $user = User::find($id);

        if (!$user) {
            // abort(404, 'Client not found');
            return redirect()->back()->with('error', 'Client not found');
        }

        $prev_picture = $user->profile_photo_path;

        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'first_name' => 'required|max:50|string',
                'last_name' => 'required|max:50|string',
                'email' => [
                    'required',
                    'max:100',
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    'unique:users,email,' . $user->id,
                ],
                'phone' => 'required|digits_between:10,15|nullable|unique:users,phone,' . $user->id,
                'dob' => 'required|date',
                'status' => 'required|boolean',
                // 'profile_photo' => $request->hasFile('profile_photo') ? 'mimes:jpeg,png,jpg' : '',
                'profile_photo' => $request->hasFile('profile_photo') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : '', // 2MB
            ], [
                'email.regex' => 'Email address is not valid formate',
                'profile_photo.max' => 'Profile photo must not be greater than 2MB'
            ]);

      

            $updated = false;
            $updatedData = [];

            try {
                if ($user->first_name !== $request->first_name) {
                    $updated = true;
                    $updatedData['first_name'] = ['old' => $user->first_name, 'new' => $request->first_name];
                    $user->first_name = $request->first_name;
                }

                if ($user->last_name !== $request->last_name) {
                    $updated = true;
                    $updatedData['last_name'] = ['old' => $user->last_name, 'new' => $request->last_name];
                    $user->last_name = $request->last_name;
                }

                if ($user->email !== $request->email) {
                    $updated = true;
                    $updatedData['email'] = ['old' => $user->email, 'new' => $request->email];
                    $user->email = $request->email;
                }

                if ($user->phone !== $request->phone) {
                    $updated = true;
                    $updatedData['phone'] = ['old' => $user->clean_phone, 'new' => $request->phone];
                    $user->phone = $request->phone;
                }

                if ($request->hasFile('profile_photo')) {
                    if (file_exists($prev_picture)) {
                        @unlink($prev_picture);
                    }

                    $file = $request->file('profile_photo');
                    $path = 'profile_photo';
                    $final_image_url = ImageHelper::customSaveImage($file, $path);
                }

                if ($user->active != $request->status) {
                    $updated = true;
                    $updatedData['status'] = ['old' => $user->active, 'new' => $request->status];
                    $user->active = $request->status;
                }

                if ($updated) {
                    $user->save();
                    $user->updated_fields = $updatedData;
                    $user->profile_update = true;

                    if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($user->email)->queue(new SendClientUpdateDetailsMail($user));
                    }

                    session()->flash('success', 'Client details updated successfully ');
                    // return view('emails.client_update_details_mail', compact('user'));

                } else {
                    session()->flash('info', 'No changes were made.');
                }

                return redirect()->route('admin.users');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                // abort(500, 'An error occurred while updating the client.');
                return redirect()->back()->with('error', 'An error occurred while updating the client');
            }
        }

        return Inertia::render('Admin/user/CreateEdit', compact('user'));
    }

    public function deleteUser($id)
    {
        PermissionHelper::abort403('Client');

        try {
            $user = User::find($id);
            if (isset($user->profile_photo_path) && file_exists($user->profile_photo_path)) {
                @unlink($user->profile_photo_path);
            }
            User::where('id', $id)->delete();

            session()->flash('success', 'Client successfully deleted');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // abort(500);
            return redirect()->back()->with('error', 'An error occurred while deleting the client');
        }
    }

    public function changeUserStatus(Request $request)
    {
        PermissionHelper::abort403('Client');

        try {
            $user = User::find($request->id);
            $user->active = !$user->active;
            $user->save();
            $user->account_status =  $user->active;
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)->queue(new SendClientUpdateDetailsMail($user));
            }
            session()->flash('success', 'Client status successfully changed');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while updating the client account status');
        }
    }

    public function clientBooking(Request $request, $id)
    {
        PermissionHelper::abort403('Client');

        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->back()->with('error', 'Client not found');
            }
            if ($user) {
                $bookings = $user->bookings()->with(['service.user', 'slot', 'service.assignedTo', 'service', 'service_session']);
                if ($request->service) {
                    $bookings = $bookings->whereHas('service', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . trim($request->service) . '%');
                    });
                }
                if ($request->date) {
                    $bookings = $bookings->whereDate('date', date('Y-m-d', strtotime($request->date)));
                }

                if (isset($request->payment_status)) {
                    $bookings = $bookings->where('payment_status', $request->payment_status);
                }
                if (isset($request->approval_status)) {
                    $bookings = $bookings->where('is_approved', $request->approval_status);
                }
                // if ($request->time) {
                //     $bookings = $bookings->whereHas('slot', function ($query) use ($request) {
                //         $query->where('start_time', 'like', '%' . trim($request->time) . '%')->orWhere('end_time', 'like', '%' . trim($request->time) . '%');
                //     });
                // }
                if ($request->fieldName && $request->shortBy) {
                    $bookings->orderBy($request->fieldName, $request->shortBy);
                }
                $perPage = $request->perPage ?? 20;
                $bookings = $bookings->paginate($perPage)->withQueryString();
            }
            return Inertia::render('Admin/user/Booking', ['user' => $user, 'bookings' => $bookings]);
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // abort(500, 'An error occurred while fetching client bookings.');
            return redirect()->back()->with('error', 'An error occurred while fetching client bookings');
        }
    }

    public function clientProviderMatching(Request $request, $id)
    {
        PermissionHelper::abort403('Client');

        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->back()->with('error', 'Client not found');
            }
            if ($user) {
                $matchings = $user->bookings()->where('is_approved', 1)->with(['service.assignedTo', 'service.user', 'service_session']);
                if ($request->service) {
                    $matchings = $matchings->whereHas('service', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . trim($request->service) . '%');
                    });
                }
                if ($request->provider) {
                    $matchings = $matchings->whereHas('service.assignedTo', function ($query) use ($request) {
                        $query->WhereRaw(
                            "concat(first_name,' ', last_name) like '%" . trim(addslashes($request->provider)) . "%' "
                        );
                    });
                }
                if ($request->date) {
                    $matchings = $matchings->whereDate('date', date('Y-m-d', strtotime($request->date)));
                }

                if ($request->fieldName && $request->shortBy) {
                    $matchings->orderBy($request->fieldName, $request->shortBy);
                }
                $perPage = $request->perPage ?? 20;
                $matchings = $matchings->paginate($perPage)->withQueryString();
            }
            return Inertia::render('Admin/user/Matching', ['user' => $user, 'matchings' => $matchings]);
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // abort(500, 'An error occurred while fetching client bookings.');
            return redirect()->back()->with('error', 'An error occurred while fetching client bookings');
        }
    }

    public function usedReferralCode(Request $request, $id)
    {
        PermissionHelper::abort403('Client');

        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->back()->with('error', 'Client not found');
            }
            if ($user) {
                $usedReferral = $user->bookings()->where('is_approved', 1)->whereNotNull('used_referral_code')->with('service');
                if (isset($request->code)) {
                    $usedReferral = $usedReferral->where('used_referral_code', 'like', '%' . trim($request->code) . '%');
                }
                if ($request->service) {
                    $usedReferral = $usedReferral->whereHas('service', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . trim($request->service) . '%');
                    });
                }
                if ($request->fieldName && $request->shortBy) {
                    $usedReferral->orderBy($request->fieldName, $request->shortBy);
                }
                $perPage = $request->perPage ?? 20;
                $usedReferral = $usedReferral->paginate($perPage)->withQueryString();
            }
            return Inertia::render('Admin/user/UsedReferral', ['usedReferrals' => $usedReferral, 'user' => $user]);
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // abort(500, 'An error occurred while fetching client bookings.');
            return redirect()->back()->with('error', 'An error occurred while fetching client bookings');
        }
    }
}
