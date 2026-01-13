<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            $companies = Company::all();
        } else {
            $companies = collect([$user->company]);
        }
        return view('invitations.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Admin,Member',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $existingInvitation = Invitation::where('email', $request->email)
            ->whereNull('accepted_at')
            ->first();

        if ($existingInvitation) {
            return back()->withErrors(['email' => 'An invitation already exists for this email']);
        }

        if ($user->isSuperAdmin()) {
            if (!$request->company_id) {
                return back()->withErrors(['company_id' => 'Company is required for SuperAdmin']);
            }
            $companyId = $request->company_id;
        } else {
            if ($request->company_id && $request->company_id != $user->company_id) {
                return back()->withErrors(['company_id' => 'You can only invite to your own company']);
            }
            $companyId = $user->company_id;
        }

              Invitation::create([
            'email' => $request->email,
            'role' => $request->role,
            'company_id' => $companyId,
            'invited_by' => $user->id,
        ]);

        return redirect()->route('invitations.create')->with('success', 'Invitation sent');
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->first();

        if (!$invitation) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid invitation']);
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function processAccept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->first();

        if (!$invitation) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid invitation']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => $request->password,
            'role' => $invitation->role,
            'company_id' => $invitation->company_id,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully');
    }
}
