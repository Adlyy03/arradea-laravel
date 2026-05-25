<?php

/**
 * Comprehensive FCM Test Script
 * Tests all aspects of FCM notification system
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\FcmToken;
use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

echo "\n";
echo str_repeat('=', 80) . "\n";
echo "  COMPREHENSIVE FCM NOTIFICATION TEST\n";
echo str_repeat('=', 80) . "\n\n";

// ============================================================================
// TEST 1: Check FCM Tokens in Database
// ============================================================================
echo "TEST 1: Checking FCM Tokens in Database\n";
echo str_repeat('-', 80) . "\n";

$totalTokens = FcmToken::count();
$activeTokens = FcmToken::active()->count();
$usersWithTokens = FcmToken::active()->distinct('user_id')->count('user_id');

echo "Total FCM tokens: {$totalTokens}\n";
echo "Active FCM tokens: {$activeTokens}\n";
echo "Users with active tokens: {$usersWithTokens}\n\n";

if ($activeTokens === 0) {
    echo "❌ FAILED: No active FCM tokens found!\n";
    echo "   Please login to the app and allow notifications.\n";
    echo "   URL: http://localhost:8000/login\n\n";
    
    echo "Checking users table for old fcm_token column...\n";
    $usersWithOldToken = User::whereNotNull('fcm_token')->count();
    echo "Users with fcm_token in users table: {$usersWithOldToken}\n";
    
    if ($usersWithOldToken > 0) {
        echo "\n⚠️  WARNING: Found tokens in users.fcm_token but not in fcm_tokens table\n";
        echo "   This means tokens were saved using old system.\n";
        echo "   Solution: Login again to save tokens to new fcm_tokens table.\n";
    }
    
    exit(1);
}

echo "✅ PASSED: Found active FCM tokens\n\n";

// Show token details
$tokens = FcmToken::active()->with('user')->get();
foreach ($tokens as $token) {
    echo "User: {$token->user->name} (ID: {$token->user_id})\n";
    echo "  Role: " . ($token->user->is_seller ? 'Seller' : 'Buyer') . "\n";
    echo "  Device: {$token->device_name}\n";
    echo "  Token: " . substr($token->token, 0, 30) . "...\n";
    echo "  Last used: " . ($token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never') . "\n\n";
}

// ============================================================================
// TEST 2: Check Firebase Configuration
// ============================================================================
echo "\nTEST 2: Checking Firebase Configuration\n";
echo str_repeat('-', 80) . "\n";

$firebaseCredentials = env('FIREBASE_CREDENTIALS');
if (!$firebaseCredentials) {
    echo "❌ FAILED: FIREBASE_CREDENTIALS not set in .env\n";
    exit(1);
}

if (!file_exists($firebaseCredentials)) {
    echo "❌ FAILED: Firebase credentials file not found: {$firebaseCredentials}\n";
    exit(1);
}

echo "✅ PASSED: Firebase credentials file exists\n";
echo "   Path: {$firebaseCredentials}\n\n";

// ============================================================================
// TEST 3: Test PushNotificationService
// ============================================================================
echo "\nTEST 3: Testing PushNotificationService\n";
echo str_repeat('-', 80) . "\n";

try {
    $pushService = app(PushNotificationService::class);
    echo "✅ PASSED: PushNotificationService instantiated successfully\n\n";
} catch (\Exception $e) {
    echo "❌ FAILED: Could not instantiate PushNotificationService\n";
    echo "   Error: {$e->getMessage()}\n";
    exit(1);
}

// ============================================================================
// TEST 4: Send Test Notification to First User
// ============================================================================
echo "\nTEST 4: Sending Test Notification\n";
echo str_repeat('-', 80) . "\n";

$firstToken = FcmToken::active()->with('user')->first();

if (!$firstToken) {
    echo "❌ FAILED: No active tokens to test\n";
    exit(1);
}

$testUser = $firstToken->user;
echo "Sending test notification to: {$testUser->name} (ID: {$testUser->id})\n";
echo "Device: {$firstToken->device_name}\n\n";

$result = $pushService->sendToUser(
    $testUser,
    '🧪 Test Notification',
    'This is a comprehensive test notification from FCM test script',
    [
        'type' => 'test',
        'test_id' => uniqid(),
        'timestamp' => now()->toIso8601String()
    ],
    asset('icons/logo-arradea.png'),
    url('/')
);

echo "Result:\n";
echo "  Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
echo "  Total: " . ($result['total'] ?? 0) . "\n";
echo "  Successful: " . ($result['successful'] ?? 0) . "\n";
echo "  Failed: " . ($result['failed'] ?? 0) . "\n";

if (!$result['success']) {
    echo "\n❌ FAILED: Could not send notification\n";
    if (isset($result['message'])) {
        echo "   Error: {$result['message']}\n";
    }
    exit(1);
}

echo "\n✅ PASSED: Test notification sent successfully!\n";
echo "   Check your browser for the notification.\n\n";

// ============================================================================
// TEST 5: Test Real Scenario - Order Notification
// ============================================================================
echo "\nTEST 5: Testing Real Scenario - Order Notification\n";
echo str_repeat('-', 80) . "\n";

// Find a recent order
$recentOrder = Order::with(['user', 'store.user'])->latest()->first();

if (!$recentOrder) {
    echo "⚠️  SKIPPED: No orders found to test\n\n";
} else {
    echo "Testing with Order #{$recentOrder->id}\n";
    echo "  Buyer: {$recentOrder->user->name}\n";
    echo "  Seller: " . ($recentOrder->store->user->name ?? 'N/A') . "\n";
    echo "  Status: {$recentOrder->status}\n\n";

    // Check if seller has active token
    $sellerTokens = $recentOrder->store->user->fcmTokens()->active()->count();
    echo "Seller has {$sellerTokens} active token(s)\n";

    if ($sellerTokens > 0) {
        echo "Sending 'New Order' notification to seller...\n";
        
        $result = $pushService->sendToUser(
            $recentOrder->store->user,
            '🛒 Pesanan Baru!',
            "Kamu mendapat pesanan baru dari {$recentOrder->user->name}",
            [
                'type' => 'new_order',
                'order_id' => (string)$recentOrder->id,
                'buyer_name' => $recentOrder->user->name
            ],
            asset('icons/logo-arradea.png'),
            url('/seller/orders')
        );

        if ($result['success']) {
            echo "✅ PASSED: Order notification sent successfully!\n\n";
        } else {
            echo "❌ FAILED: Could not send order notification\n";
            echo "   Error: " . ($result['message'] ?? 'Unknown error') . "\n\n";
        }
    } else {
        echo "⚠️  SKIPPED: Seller has no active tokens\n\n";
    }
}

// ============================================================================
// TEST 6: Test Broadcast to All Users
// ============================================================================
echo "\nTEST 6: Testing Broadcast to All Users\n";
echo str_repeat('-', 80) . "\n";

$allUsersWithTokens = User::has('fcmTokens')->count();
echo "Users with active tokens: {$allUsersWithTokens}\n";

if ($allUsersWithTokens > 0) {
    echo "Sending broadcast notification to all users...\n";
    
    $userIds = FcmToken::active()->pluck('user_id')->unique()->toArray();
    
    $result = $pushService->sendToUsers(
        $userIds,
        '📢 Broadcast Test',
        'This is a broadcast test notification to all users',
        [
            'type' => 'broadcast',
            'test_id' => uniqid()
        ],
        asset('icons/logo-arradea.png'),
        url('/')
    );

    echo "Result:\n";
    echo "  Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "  Total: " . ($result['total'] ?? 0) . "\n";
    echo "  Successful: " . ($result['successful'] ?? 0) . "\n";
    echo "  Failed: " . ($result['failed'] ?? 0) . "\n";

    if ($result['success']) {
        echo "\n✅ PASSED: Broadcast notification sent successfully!\n\n";
    } else {
        echo "\n❌ FAILED: Could not send broadcast notification\n\n";
    }
} else {
    echo "⚠️  SKIPPED: No users with active tokens\n\n";
}

// ============================================================================
// TEST 7: Check Service Worker Registration
// ============================================================================
echo "\nTEST 7: Service Worker Check\n";
echo str_repeat('-', 80) . "\n";

$swFile = public_path('firebase-messaging-sw.js');
if (file_exists($swFile)) {
    echo "✅ PASSED: firebase-messaging-sw.js exists\n";
    echo "   Path: {$swFile}\n";
    echo "   Size: " . filesize($swFile) . " bytes\n\n";
} else {
    echo "❌ FAILED: firebase-messaging-sw.js not found\n";
    echo "   Expected path: {$swFile}\n\n";
}

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n" . str_repeat('=', 80) . "\n";
echo "  TEST SUMMARY\n";
echo str_repeat('=', 80) . "\n\n";

echo "✅ All tests completed!\n\n";

echo "Next steps:\n";
echo "1. Check your browser for test notifications\n";
echo "2. Check Laravel logs: storage/logs/laravel.log\n";
echo "3. Check browser console for FCM logs\n";
echo "4. Check service worker console (DevTools → Application → Service Workers)\n\n";

echo "If notifications don't appear:\n";
echo "1. Make sure service worker is registered correctly\n";
echo "2. Check notification permission is granted\n";
echo "3. Check browser console for errors\n";
echo "4. Try opening: http://localhost:8000/fix-sw-now.html\n\n";

echo str_repeat('=', 80) . "\n\n";
