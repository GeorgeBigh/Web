<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Company;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = auth()->user();
        $companies = $user->companies;
        return view('profile.edit', compact('user', 'companies'));
    }
    

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
    
        // Validate and update user profile details
        $user->fill($request->validated());
    
        // Handle profile photo upload if provided
        if ($request->hasFile('photo_of_user')) {
            $request->validate([
                
                'photo_of_user' => ['image', 'max:2048'] // Example validation rules for image file
            ]);
    
            // Store uploaded photo in storage
            $path = $request->file('photo_of_user')->store('profile-photos', 'public');
    
            // Update user's photo path in database


            $user->photo_of_user = $path;
        }
    
        // Reset email verification if email is updated
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        // Save user model
        $user->save();
    
        // Redirect back to profile edit page with success message
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showUserCompanies()
    {
        $user = auth()->user();
        $companies = $user->companies;

        return view('company.user_companies', compact('companies'));
    }
}
