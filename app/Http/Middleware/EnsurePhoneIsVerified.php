<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->phone_verified_at) {
            return redirect()->route('verification.phone.notice')
                ->with('status', 'Silakan verifikasi nomor HP kamu terlebih dahulu.');
        }

        return $next($request);
    }
}