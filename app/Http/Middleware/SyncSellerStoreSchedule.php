<?php

namespace App\Http\Middleware;

use App\Models\Store;
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

        if (! $user || $user->role !== 'seller') {
            return;
        }

        $store = $user->store;

        if (! $store) {
            return;
        }

        // Jika auto_schedule false, toko buka 24 jam (skip sync)
        if (! $store->auto_schedule) {
            if ($store->store_status !== 'open') {
                Store::whereKey($store->id)->update(['store_status' => 'open']);
            }
            return;
        }

        // Jika tidak ada jadwal, skip
        if (! $store->open_time || ! $store->close_time) {
            return;
        }

        $now = now('Asia/Jakarta')->format('H:i:s');
        $openTime = (string) $store->open_time;
        $closeTime = (string) $store->close_time;

        // Handle normal hours (e.g., 08:00 - 22:00) and overnight hours (e.g., 22:00 - 02:00)
        $isOpen = $openTime <= $closeTime
            ? ($now >= $openTime && $now <= $closeTime)
            : ($now >= $openTime || $now <= $closeTime);

        $nextStatus = $isOpen ? 'open' : 'closed';

        if ($store->store_status !== $nextStatus) {
            Store::whereKey($store->id)->update(['store_status' => $nextStatus]);
        }
    }
}