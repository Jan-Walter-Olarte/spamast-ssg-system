<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: 'role:Admin' or 'role:Officer,Adviser'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Stop with 403 Forbidden unless the user has one of the allowed roles.
        abort_unless($request->user()?->hasRole(...$roles), 403);

        return $next($request);
    }
}
