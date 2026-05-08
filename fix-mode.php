<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update all sellers to have preferred_mode = 'seller'
$updated = DB::table('users')
    ->where('is_seller', true)
    ->where('seller_status', 'approved')
    ->update(['preferred_mode' => 'seller']);

echo "✅ Updated {$updated} seller(s) with preferred_mode = 'seller'\n";

// Show all sellers
$sellers = DB::table('users')
    ->where('is_seller', true)
    ->select('id', 'name', 'is_seller', 'seller_status', 'preferred_mode')
    ->get();

echo "\n📋 All Sellers:\n";
foreach ($sellers as $seller) {
    echo "  - ID: {$seller->id}, Name: {$seller->name}, Status: {$seller->seller_status}, Mode: {$seller->preferred_mode}\n";
}
