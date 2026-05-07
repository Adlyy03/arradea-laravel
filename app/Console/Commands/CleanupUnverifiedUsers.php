<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified {--hours=24 : Hours before deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users who have not verified their phone number within specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);

        // Find users who:
        // 1. Have not verified their phone (phone_verified_at is null)
        // 2. Are not admin
        // 3. Created more than X hours ago
        $unverifiedUsers = User::whereNull('phone_verified_at')
            ->where('role', '!=', 'admin')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found to cleanup.');
            return 0;
        }

        $count = $unverifiedUsers->count();
        
        $this->info("Found {$count} unverified user(s) older than {$hours} hours.");
        
        if ($this->confirm('Do you want to delete these users?', true)) {
            foreach ($unverifiedUsers as $user) {
                $this->line("Deleting user: {$user->name} ({$user->phone}) - Created: {$user->created_at}");
                $user->delete();
            }
            
            $this->info("Successfully deleted {$count} unverified user(s).");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
