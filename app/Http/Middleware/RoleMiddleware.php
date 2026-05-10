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

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect('/login');
        }

        // Get active mode from session (for mode switching feature)
        $activeMode = session('active_mode', $user->preferred_mode ?? 'buyer');

        $allowed = collect($roles)
            ->contains(function (string $role) use ($user, $activeMode): bool {
                if ($role === 'buyer') {
                    return true; // Everyone can access buyer routes
                }

                if ($role === 'seller') {
                    // Check if user is seller AND currently in seller mode
                    return (bool) $user->is_seller 
                        && $user->seller_status === 'approved'
                        && $activeMode === 'seller';
                }

                if ($role === 'admin') {
                    return $user->role === 'admin';
                }

                return $user->role === $role;
            });

        if (! $allowed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki peran atau mode yang diperlukan.',
                ], Response::HTTP_FORBIDDEN);
            }
            
            // For web routes, redirect to home with error
            return redirect('/')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman ini. Pastikan Anda dalam mode yang sesuai.']);
        }

        return $next($request);
    }
}
