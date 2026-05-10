<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync store status every minute (jam buka/tutup otomatis)
Schedule::command('stores:sync-schedules')->everyMinute();

// Cleanup unverified users daily at 2 AM (after 24 hours of no verification)
Schedule::command('users:cleanup-unverified --hours=24')->dailyAt('02:00');
