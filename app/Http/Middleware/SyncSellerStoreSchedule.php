<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class SyncSellerStoreSchedule
{
    public function handle(Request $request, Closure $next)
    {
        $this->syncCurrentSeller($request);

        return $next($request);
    }

    protected function syncCurrentSeller(Request $request): void
    {
        $user = $request->user();

        if (! $user || $user->role !== 'seller' || ! $user->auto_schedule || ! $user->open_time || ! $user->close_time) {
            return;
        }

        $now = now('Asia/Jakarta')->format('H:i:s');
        $openTime = (string) $user->open_time;
        $closeTime = (string) $user->close_time;

        $isOpen = $openTime <= $closeTime
            ? ($now >= $openTime && $now <= $closeTime)
            : ($now >= $openTime || $now <= $closeTime);

        $nextStatus = $isOpen ? 'open' : 'closed';

        if ($user->store_status !== $nextStatus) {
            User::whereKey($user->id)->update(['store_status' => $nextStatus]);
        }
    }
}