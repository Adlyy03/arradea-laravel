<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncStoreSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stores:sync-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync store open/closed status based on schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now('Asia/Jakarta')->format('H:i:s');
        $updated = 0;

        User::query()
            ->where('role', 'seller')
            ->where('auto_schedule', true)
            ->whereNotNull('open_time')
            ->whereNotNull('close_time')
            ->select(['id', 'name', 'open_time', 'close_time', 'store_status'])
            ->chunkById(200, function ($sellers) use (&$updated, $now) {
                foreach ($sellers as $seller) {
                    $openTime = (string) $seller->open_time;
                    $closeTime = (string) $seller->close_time;

                    // Handle normal hours (e.g., 08:00 - 22:00) and overnight hours (e.g., 22:00 - 02:00)
                    $isOpen = $openTime <= $closeTime
                        ? ($now >= $openTime && $now <= $closeTime)
                        : ($now >= $openTime || $now <= $closeTime);

                    $nextStatus = $isOpen ? 'open' : 'closed';

                    if ($seller->store_status !== $nextStatus) {
                        User::whereKey($seller->id)->update(['store_status' => $nextStatus]);
                        $updated++;
                        
                        if ($this->output->isVerbose()) {
                            $this->info("Updated {$seller->name} to {$nextStatus}");
                        }
                    }
                }
            });

        $this->info("Synced {$updated} store(s) status.");

        return Command::SUCCESS;
    }
}
