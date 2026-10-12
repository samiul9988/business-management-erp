<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets the maintenance-mode toggle page be reached by a logged-in user, or
 * by anyone holding the ACTIVATION_SECRET — so it's never possible to get
 * locked out of switching the site back on, even if login itself is broken.
 */
class AllowActivationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        $secret = config('app.activation_secret');
        $given = $request->input('key');

        if ($secret && $given && hash_equals((string) $secret, (string) $given)) {
            return $next($request);
        }

        abort(404);
    }
}
