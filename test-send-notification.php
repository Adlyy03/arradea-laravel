<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n========================================\n";
echo "  TEST SEND FCM NOTIFICATION\n";
echo "========================================\n\n";

// Get user with FCM token
$users = App\Models\User::has('fcmTokens')->with('fcmTokens')->get();

if ($users->isEmpty()) {
    echo "❌ No users with FCM tokens found!\n";
    echo "   Please login and allow notifications first.\n\n";
    exit(1);
}

echo "Users with FCM tokens:\n";
foreach ($users as $user) {
    $tokenCount = $user->fcmTokens()->active()->count();
    echo "  {$user->id}. {$user->name} ({$tokenCount} active tokens)\n";
}

echo "\nEnter user ID to send test notification: ";
$userId = trim(fgets(STDIN));

$user = App\Models\User::find($userId);

if (!$user) {
    echo "❌ User not found!\n\n";
    exit(1);
}

$tokenCount = $user->fcmTokens()->active()->count();

if ($tokenCount === 0) {
    echo "❌ User has no active FCM tokens!\n\n";
    exit(1);
}

echo "\n========================================\n";
echo "  SENDING TEST NOTIFICATION\n";
echo "========================================\n\n";

echo "To: {$user->name} (ID: {$user->id})\n";
echo "Active tokens: {$tokenCount}\n\n";

$service = app(App\Services\PushNotificationService::class);

$result = $service->sendToUser(
    $user,
    '🧪 Test Notification',
    'This is a test notification from Arradea. If you see this, FCM is working!',
    [
        'type' => 'test',
        'timestamp' => now()->toIso8601String(),
    ],
    asset('icons/logo-arradea.png'),
    url('/')
);

echo "\n========================================\n";
echo "  RESULT\n";
echo "========================================\n\n";

if ($result['success']) {
    echo "✅ SUCCESS!\n\n";
    echo "Total tokens: {$result['total']}\n";
    echo "Successful: {$result['successful']}\n";
    echo "Failed: {$result['failed']}\n";
    
    if (!empty($result['invalid_tokens'])) {
        echo "\nInvalid tokens:\n";
        foreach ($result['invalid_tokens'] as $token) {
            echo "  - " . substr($token, 0, 30) . "...\n";
        }
    }
    
    echo "\n========================================\n";
    echo "  WHAT TO CHECK\n";
    echo "========================================\n\n";
    echo "1. Check browser console for:\n";
    echo "   📬 FOREGROUND MESSAGE RECEIVED\n\n";
    echo "2. Check if notification appears:\n";
    echo "   - In browser (if app is open)\n";
    echo "   - In Windows notification center (if app is minimized)\n\n";
    echo "3. Check Laravel log:\n";
    echo "   tail -f storage/logs/laravel.log | grep FCM\n\n";
    echo "4. Check Service Worker console:\n";
    echo "   DevTools → Application → Service Workers\n\n";
    
} else {
    echo "❌ FAILED!\n\n";
    echo "Error: {$result['message']}\n\n";
}

echo "========================================\n\n";
