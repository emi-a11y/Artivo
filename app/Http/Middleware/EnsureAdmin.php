<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Lets only users with the Admin role into the admin area. */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user() && $request->user()->isAdmin(), 403, 'Admin access only.');

        return $next($request);
    }
}
