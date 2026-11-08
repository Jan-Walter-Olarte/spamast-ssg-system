<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show the login page.
    public function showLoginForm()
    {
        return view('login');
    }

    // Check the credentials and start the session.
    public function login(Request $request): RedirectResponse
    {
        // 1. Validate the form fields.
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        // 2. Try to log in. Only email and password are checked against the database.
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        // 3. Block inactive accounts, even if the password was correct.
        if (! $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Your account is inactive or awaiting Admin approval.'])
                ->onlyInput('email');
        }

        // 4. New session id after login, to stop session fixation.
        $request->session()->regenerate();

        // 5. Send each person to their own dashboard.
        $user = $request->user();

        return redirect()->intended(route($user->dashboardRoute()));
    }

    // End the session.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
