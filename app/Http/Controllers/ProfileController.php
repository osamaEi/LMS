<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'national_id' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', __('Profile updated successfully!'));
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', __('Password updated successfully!'));
    }

    /**
     * Update the user's avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete('avatars/' . $user->profile_photo);
        }

        // Store new avatar — save only the filename; displayed via asset('storage/avatars/{profile_photo}')
        $file     = $request->file('avatar');
        $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
        $file->storeAs('avatars', $filename, 'public');
        $user->update(['profile_photo' => $filename]);

        return redirect()->route('profile.edit')
            ->with('success', __('Avatar updated successfully!'));
    }
}
