<?php
// Temporary helper - jalankan di browser: http://127.0.0.1:8000/migration_helper.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true,
    ]);
    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "<pre>Error: " . $e->getMessage() . "</pre>";
}
?>
