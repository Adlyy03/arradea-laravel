<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles e.g. 'admin', 'seller', 'buyer'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. You do not have the required role.',
                ], Response::HTTP_FORBIDDEN);
            }
            
            // For web routes, redirect to home with error
            return redirect('/')->withErrors(['access' => 'You do not have permission to access this page.']);
        }

        return $next($request);
    }
}
