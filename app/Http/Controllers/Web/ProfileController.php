<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the authenticated user's profile.
     */
    public function index(Request $request)
    {
        return view('web.profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Show the form for editing the authenticated user's profile.
     */
    public function edit(Request $request)
    {
        return view('web.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's profile, including address fields
     * that will later be reused to pre-fill the checkout form.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone,'.$user->id],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'avatar' => ['nullable', 'image', 'max:2048'],

            // Address fields — reused by checkout
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        $user->update(['avatar' => null]);

        return back()->with('status', 'Avatar removed.');
    }
}
