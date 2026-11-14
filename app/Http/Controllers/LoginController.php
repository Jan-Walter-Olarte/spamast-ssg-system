<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // The most wrong passwords allowed per minute for one email from one computer.
    private const MAX_ATTEMPTS = 5;

    // Show the login page.
    public function showLoginForm()
    {
        return view('login');
    }

    // Check the credentials and start the session.
    public function login(Request $request): RedirectResponse
    {
        // 1. Validate the form fields.
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        // 2. Too many wrong passwords? Stop before even checking this one.
        $key = Str::lower((string) $request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->onlyInput('email');
        }

        // 3. Try to log in. Only email and password are checked against the database.
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Count this failed attempt for one minute.
            RateLimiter::hit($key, 60);

            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        // 4. The password was right: forget the failed attempts.
        RateLimiter::clear($key);

        // 5. Block inactive accounts, even if the password was correct.
        if (! $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Your account is inactive or awaiting Admin approval.'])
                ->onlyInput('email');
        }

        // 6. New session id after login, to stop session fixation.
        $request->session()->regenerate();

        // 7. Send each person to their own dashboard.
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
