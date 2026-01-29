<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuth
{
    /**
     * Handle an incoming request - authenticate if token is present, but don't require it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to authenticate if Authorization header is present
        if ($request->bearerToken()) {
            try {
                // Attempt Sanctum authentication
                $user = $request->user('sanctum');
                if ($user) {
                    $request->setUserResolver(fn () => $user);
                }
            } catch (\Exception $e) {
                // Token is invalid, but we allow guest access
                // Do nothing, continue as guest
            }
        }

        return $next($request);
    }
}
