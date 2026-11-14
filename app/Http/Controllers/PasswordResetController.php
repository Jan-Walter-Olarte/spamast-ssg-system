<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Show the "forgot your password?" form.
    public function request()
    {
        return view('auth.forgot-password');
    }

    // Send the reset link, if the email belongs to an account.
    public function email(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // We ignore the result on purpose: the answer must not reveal whether the email exists.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'If that email belongs to an account, a password reset link has been sent. The link works for 60 minutes.');
    }

    // Show the "choose a new password" form that the link opens.
    public function reset(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // Check the token, then save the new password.
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                // forceFill saves these columns even though they are not all in the Fillable list.
                $user->forceFill([
                    'password'             => Hash::make($password),
                    'remember_token'       => Str::random(60), // ends any "remember me" login
                    'must_change_password' => false,           // they just chose their own password
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return redirect()->route('login')
                ->with('success', 'Your password was reset. You can now log in with the new password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
    }
}
