<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // Show the "set a new password" page.
    public function edit()
    {
        return view('auth.change-password');
    }

    // Save the new password and clear the "must change" flag.
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // current_password checks it against the logged-in user's real password.
            'current_password' => ['required', 'current_password'],
            // The new password must be 8+ characters, typed twice, and different from the old one.
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $user = $request->user();

        $user->password             = Hash::make($data['password']);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Your password was changed. You can now use the system.');
    }
}
