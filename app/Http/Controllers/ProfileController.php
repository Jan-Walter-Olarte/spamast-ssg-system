<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Show the profile form for the logged-in user.
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Save the changes of the logged-in user.
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user(); // never trust a user ID from the form

        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => [
                'required', 'email:rfc', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // Optional password change
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
            // Optional profile photo: an image of at most 2 MB (2048 kilobytes)
            'photo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Set only the safe fields, one by one.
        $user->first_name  = $data['first_name'];
        $user->middle_name = $data['middle_name'] ?? null;
        $user->last_name   = $data['last_name'];
        $user->email       = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // A new photo was uploaded: remove the old file, then store the new one.
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // store() returns the path inside the disk, for example "avatars/Xk3F....jpg".
            $user->photo = $request->file('photo')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
