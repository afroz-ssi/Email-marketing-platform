<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        PermissionHelper::abort403('Lead');
        try {
            $leads = Lead::query();

            if ($request->name) {
                $leads = $leads->where('name', 'like', '%' . trim($request->name) . '%');
            }

            if ($request->company) {
                $leads = $leads->where('company', 'like', '%' . trim($request->company) . '%');
            }

            if ($request->email) {
                $leads = $leads->where('email', 'like', '%' . trim($request->email) . '%');
            }

            if ($request->phone) {
                $leads = $leads->where('phone', 'like', '%' . trim($request->phone) . '%');
            }

            if ($request->fieldName && $request->shortBy) {
                $leads->orderBy($request->fieldName, $request->shortBy);
            }

            $perPage = $request->perPage ?? 20;
            $leads = $leads->latest()->paginate($perPage)->withQueryString();

            return Inertia::render('Admin/lead/List', compact('leads'));
        } catch (\Exception $e) {
            Log::error(" :: LEAD EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            abort(500);
        }
    }

    public function create(Request $request)
    {
        PermissionHelper::abort403('Lead');

        if ($request->isMethod('post')) {
            $request->validate([
                'fullname' => 'required|max:100|string',
                'company' => 'required|max:100|string',
                'website' => 'required|url|max:255',
                'email' => ['required', 'email', 'max:100', Rule::unique('leads')],
                'phone' => ['required', 'digits_between:10,15', 'numeric', Rule::unique('leads')],
            ]);

            try {
                Lead::create([
                    'name' => $request->fullname,
                    'company' => $request->company,
                    'website' => $request->website,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);

                session()->flash('success', 'Lead successfully created');
                return redirect()->route('admin.leads');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                abort(500);
            }
        }

        return Inertia::render('Admin/lead/CreateEdit');
    }

    public function edit(Request $request, $id)
    {
        PermissionHelper::abort403('Lead');

        $lead = Lead::find($id);

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'fullname' => 'required|max:100|string',
                'company' => 'required|max:100|string',
                'website' => 'nullable|url|max:255',
                'email' => ['required', 'email', 'max:100', Rule::unique('leads')->ignore($lead->id)],
                'phone' => ['required', 'digits_between:10,15', 'numeric', Rule::unique('leads')->ignore($lead->id)],
            ]);

            try {

                $lead->update([
                    'name' => $request->fullname,
                    'company' => $request->company,
                    'website' => $request->website,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);

                session()->flash('success', 'Lead successfully updated');
                return redirect()->route('admin.leads');
            } catch (\Exception $e) {
                Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                return redirect()->back()->with('error', 'An error occurred while updating the lead');
            }
        }

        return Inertia::render('Admin/lead/CreateEdit', compact('lead'));
    }

    public function destroy($id)
    {
        PermissionHelper::abort403('Lead');

        try {
            $lead = Lead::find($id);

            if (!$lead) {
                return redirect()->back()->with('error', 'Lead not found');
            }

            $lead->delete();

            session()->flash('success', 'Lead successfully deleted');
            return back();
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while deleting the lead');
        }
    }

    public function show($id)
    {
        PermissionHelper::abort403('Lead');

        try {
            $lead = Lead::findOrFail($id);
            return Inertia::render('Admin/lead/Show', compact('lead'));
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Lead not found');
        }
    }
}
