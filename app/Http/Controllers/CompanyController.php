<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteUserMail;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function showAddCompanyForm()
    {
        return view('company.create_company');
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
        ]);
    
        Company::create([
            'company_name' => $request->company_name,
            'user_id' => $request->user_id, // Add the ID of the authenticated user
        ]);
    
        return redirect()->route('company.add')->with('status', 'Company added successfully.');
    }

    public function showAssignCompanyForm()
    {
        $users = User::all();
        $companies = Company::all();

        return view('company.assign', compact('users', 'companies'));
    }

    public function assignCompany(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);
        
        $user = User::find($request->user_id);
        $company = Company::find($request->company_id);
        
        // Check if the user is already assigned to the company
        if (!$user->companies->contains($company)) {
            $user->companies()->attach($company);
            $message = 'Company assigned successfully.';
        } else {
            $message = 'User is already assigned to this company.';
        }
        
        return redirect()->back()->with('status', $message);
        
    }

    public function showUserCompanies()
    {
        $user = Auth::user();
        $roleId = 1; // Replace with the actual role ID for admins

        // Get the role IDs for the current user
        $userRoleIds = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->pluck('role_id');

        if ($userRoleIds->contains($roleId)) {
            // Admins see all companies and their users
            $companies = Company::with('users')->get();
            $companyId = null; // Admins may not need to specify a company
        } else {
            // Non-admins see only the companies they are assigned to
            $companies = Company::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('users')->get();
            $companyId = $companies->first()->id ?? null; // Set the company ID for non-admins
        }

        return view('company.user_companies', compact('companies', 'companyId'));
    }


    public function inviteUser(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'company_id' => 'required|exists:companies,id',
    ]);

    $company = Company::find($request->company_id);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // User is not registered, create a new user and generate a temporary password
        $password = \Str::random(8);
        $user = User::create([
            'email' => $request->email,
            'password' => \Hash::make($password),
            'name' => 'Default Name', // Provide a default name
        ]);

        // Create an invitation with the temporary password
        $invitation = Invitation::create([
            'email' => $user->email,
            'company_id' => $company->id,
            'token' => \Str::random(32),
            'password' => $password, // Store the temporary password
        ]);

        // Send invitation email with temporary password
        Mail::to($user->email)->send(new InviteUserMail($invitation, $password));
        return redirect()->back()->with('status', 'Invitation sent with registration details.');
    } else {
        // User is already registered, check if they are already assigned
        if ($company->users()->where('users.id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'User is already assigned to this company.');
        }

        // Create an invitation
        $invitation = Invitation::create([
            'email' => $user->email,
            'company_id' => $company->id,
            'token' => \Str::random(32),
            'password' => null, // No password needed for already registered users
        ]);

        // Send invitation email without a temporary password
        Mail::to($user->email)->send(new InviteUserMail($invitation));
        return redirect()->back()->with('status', 'Invitation sent to existing user.');
    }
}

    

    public function acceptInvitation(Request $request, $token)
{
    // Find the invitation by token
    $invitation = Invitation::where('token', $token)->first();

    if (!$invitation) {
        return redirect('/')->with('status', 'Invalid or expired invitation token.');
    }

    // Find the user by email from the invitation
    $user = User::where('email', $invitation->email)->first();

    if (!$user) {
        // Optionally create the user
        $password = \Str::random(8); // Generate a random password
        $user = User::create([
            'email' => $invitation->email,
            'password' => bcrypt($password), // Set the password
        ]);

        // Optionally send password setup email
        Mail::to($user->email)->send(new PasswordSetupMail($user, $password));
    }

    // Find the company
    $company = Company::find($invitation->company_id);

    if (!$company) {
        return redirect('/')->with('status', 'Company not found.');
    }

    // Check if user is already assigned to the company
    $isAssigned = $company->users()->where('users.id', $user->id)->exists();

    if ($isAssigned) {
        return redirect('/')->with('status', 'User is already assigned to this company.');
    }

    // Attach user to the company
    $company->users()->attach($user->id);

    // Delete the invitation
    $invitation->delete();

    return redirect('/')->with('status', 'Invitation accepted successfully.');
}



public function deleteUserFromCompany($companyId, $userId)
{
    $company = Company::findOrFail($companyId);
    $user = User::findOrFail($userId);

    // Detach the user from the company
    $company->users()->detach($user->id);

    return redirect()->back()->with('status', 'User removed from company successfully.');
}

}

