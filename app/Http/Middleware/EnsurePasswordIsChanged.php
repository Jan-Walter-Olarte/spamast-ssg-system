<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * A user who still has a temporary password may only see the password form and log out.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && ! $request->routeIs('password.change', 'password.change.update', 'logout')) {
            return redirect()->route('password.change')
                ->with('error', 'For your security, please set a new password before continuing.');
        }

        return $next($request);
    }
}
