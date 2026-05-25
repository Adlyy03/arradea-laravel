<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n========================================\n";
echo "  CHECKING FCM TOKENS\n";
echo "========================================\n\n";

// Check all users with FCM tokens
$users = App\Models\User::has('fcmTokens')->with('fcmTokens')->get();

echo "Total users with FCM tokens: " . $users->count() . "\n\n";

foreach ($users as $user) {
    $activeTokens = $user->fcmTokens()->active()->count();
    $totalTokens = $user->fcmTokens()->count();
    
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "  Role: " . ($user->is_seller ? 'Seller' : ($user->role === 'admin' ? 'Admin' : 'Buyer')) . "\n";
    echo "  Active tokens: {$activeTokens}\n";
    echo "  Total tokens: {$totalTokens}\n";
    
    if ($activeTokens > 0) {
        $tokens = $user->fcmTokens()->active()->get();
        foreach ($tokens as $token) {
            echo "    - Token: " . substr($token->token, 0, 30) . "...\n";
            echo "      Created: " . $token->created_at->diffForHumans() . "\n";
        }
    }
    echo "\n";
}

echo "========================================\n";
echo "  Checking recent orders...\n";
echo "========================================\n\n";

$recentOrders = App\Models\Order::with(['user', 'store.user'])->latest()->take(5)->get();

foreach ($recentOrders as $order) {
    echo "Order #{$order->id}\n";
    echo "  Buyer: {$order->user->name} (ID: {$order->user_id})\n";
    echo "  Seller: " . ($order->store->user->name ?? 'N/A') . " (ID: " . ($order->store->user_id ?? 'N/A') . ")\n";
    echo "  Status: {$order->status}\n";
    echo "  Created: " . $order->created_at->diffForHumans() . "\n";
    
    // Check if seller has FCM token
    if ($order->store && $order->store->user) {
        $sellerTokens = $order->store->user->fcmTokens()->active()->count();
        echo "  Seller has {$sellerTokens} active FCM token(s)\n";
    }
    echo "\n";
}

echo "========================================\n\n";
