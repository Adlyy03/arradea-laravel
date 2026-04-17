<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncSellerStoreStatusCommand extends Command
{
    protected $signature = 'stores:sync-status';

    protected $description = 'Sync seller store_status based on open_time and close_time in Asia/Jakarta timezone';

    public function handle(): int
    {
        $now = now('Asia/Jakarta')->format('H:i:s');
        $updated = 0;

        User::query()
            ->where('role', 'seller')
            ->where('auto_schedule', true)
            ->whereNotNull('open_time')
            ->whereNotNull('close_time')
            ->select(['id', 'open_time', 'close_time', 'store_status'])
            ->chunkById(200, function ($sellers) use (&$updated, $now) {
                foreach ($sellers as $seller) {
                    $openTime = (string) $seller->open_time;
                    $closeTime = (string) $seller->close_time;

                    $isOpen = $openTime <= $closeTime
                        ? ($now >= $openTime && $now <= $closeTime)
                        : ($now >= $openTime || $now <= $closeTime);

                    $nextStatus = $isOpen ? 'open' : 'closed';

                    if ($seller->store_status !== $nextStatus) {
                        User::whereKey($seller->id)->update(['store_status' => $nextStatus]);
                        $updated++;
                    }
                }
            });

        $this->info("Synced seller statuses. Updated rows: {$updated}");

        return self::SUCCESS;
    }
}