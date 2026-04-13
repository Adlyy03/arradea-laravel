<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureArradeaUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $hasValidRegion = strcasecmp((string) $user->wilayah, 'Arradea') === 0;
        $hasActiveCode = $user->accessCode && $user->accessCode->is_active;

        if ($hasValidRegion && $hasActiveCode) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda tidak berada di wilayah Arradea atau kode akses tidak aktif.',
            ], Response::HTTP_FORBIDDEN);
        }

        auth()->logout();

        return redirect('/login')->withErrors([
            'access' => 'Akses ditolak. Akun Anda tidak memenuhi syarat wilayah Arradea.',
        ]);
    }
}
