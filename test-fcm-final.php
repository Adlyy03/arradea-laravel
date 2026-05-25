<?php

/**
 * Final FCM Test - Send notification and wait for confirmation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FcmToken;
use App\Services\PushNotificationService;

echo "\n";
echo str_repeat('=', 80) . "\n";
echo "  FINAL FCM TEST - SEND & VERIFY\n";
echo str_repeat('=', 80) . "\n\n";

// Get active tokens
$tokens = FcmToken::active()->with('user')->get();

if ($tokens->isEmpty()) {
    echo "❌ No active FCM tokens found!\n";
    echo "Please open: http://localhost:8000/auto-fix-and-test.html\n\n";
    exit(1);
}

echo "Found " . $tokens->count() . " active token(s)\n\n";

foreach ($tokens as $token) {
    echo "User: {$token->user->name} (ID: {$token->user_id})\n";
    echo "  Token: " . substr($token->token, 0, 30) . "...\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "Sending test notification to ALL users...\n";
echo str_repeat('=', 80) . "\n\n";

$pushService = app(PushNotificationService::class);

$userIds = $tokens->pluck('user_id')->unique()->toArray();

$result = $pushService->sendToUsers(
    $userIds,
    '🎉 FINAL TEST',
    'This is the final FCM test. If you see this, FCM is working!',
    [
        'type' => 'final_test',
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
echo "  Failed: " . ($result['failed'] ?? 0) . "\n\n";

if ($result['success']) {
    echo str_repeat('=', 80) . "\n";
    echo "✅ NOTIFICATION SENT SUCCESSFULLY!\n";
    echo str_repeat('=', 80) . "\n\n";
    
    echo "Check your browser:\n";
    echo "  1. If tab is ACTIVE (foreground):\n";
    echo "     - Check page console for: 📬 MESSAGE RECEIVED!!!\n";
    echo "     - Notification should appear\n\n";
    
    echo "  2. If tab is INACTIVE (background):\n";
    echo "     - Open DevTools → Application → Service Workers\n";
    echo "     - Click 'firebase-messaging-sw.js' link\n";
    echo "     - Check SW console for: Background message received\n";
    echo "     - Notification should appear in Windows notification center\n\n";
    
    echo "  3. If nothing appears:\n";
    echo "     - Check if browser tab is open\n";
    echo "     - Check if notification permission is granted\n";
    echo "     - Check if service worker is active\n\n";
    
    echo str_repeat('=', 80) . "\n\n";
} else {
    echo "❌ FAILED: " . ($result['message'] ?? 'Unknown error') . "\n\n";
}
