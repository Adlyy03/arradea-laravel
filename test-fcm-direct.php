<?php

/**
 * Direct FCM Test Script
 * 
 * Usage:
 * 1. Get your FCM token from fcm-debug.html
 * 2. Run: php test-fcm-direct.php YOUR_TOKEN_HERE
 * 3. Check browser for notification
 */

require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

echo "================================================================================\n";
echo "🔥 DIRECT FCM TEST\n";
echo "================================================================================\n\n";

// Get token from command line or use latest from database
$token = $argv[1] ?? null;

if (!$token) {
    echo "📋 No token provided, getting latest from database...\n\n";
    
    // Load Laravel
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $latestToken = \App\Models\FcmToken::active()->latest()->first();
    
    if (!$latestToken) {
        echo "❌ No active tokens found in database!\n";
        echo "\n💡 Solution:\n";
        echo "   1. Open: http://localhost:8000/fcm-debug.html\n";
        echo "   2. Click 'Initialize FCM'\n";
        echo "   3. Copy the token\n";
        echo "   4. Run: php test-fcm-direct.php YOUR_TOKEN\n\n";
        exit(1);
    }
    
    $token = $latestToken->token;
    echo "✅ Using token from database:\n";
    echo "   User ID: {$latestToken->user_id}\n";
    echo "   Token: " . substr($token, 0, 50) . "...\n\n";
} else {
    echo "✅ Using provided token:\n";
    echo "   Token: " . substr($token, 0, 50) . "...\n\n";
}

echo "================================================================================\n";
echo "📤 SENDING NOTIFICATION\n";
echo "================================================================================\n\n";

try {
    // Initialize Firebase
    echo "1️⃣ Initializing Firebase...\n";
    $factory = (new Factory)->withServiceAccount(__DIR__.'/arradea-marketplace-firebase-adminsdk.json');
    $messaging = $factory->createMessaging();
    echo "   ✅ Firebase initialized\n\n";

    // Build notification
    echo "2️⃣ Building notification...\n";
    $notification = Notification::create(
        '🎯 Direct Test Notification',
        'This notification was sent directly to your token!'
    );
    echo "   ✅ Notification built\n\n";

    // Build message
    echo "3️⃣ Building message...\n";
    $message = CloudMessage::new()
        ->withNotification($notification)
        ->withData([
            'type' => 'direct_test',
            'timestamp' => date('Y-m-d H:i:s'),
            'click_action' => 'http://localhost:8000'
        ]);
    echo "   ✅ Message built\n\n";

    // Send to token
    echo "4️⃣ Sending to FCM...\n";
    echo "   Token: " . substr($token, 0, 30) . "...\n";
    
    $result = $messaging->send($message->withChangedTarget('token', $token));
    
    echo "\n";
    echo "================================================================================\n";
    echo "✅ NOTIFICATION SENT SUCCESSFULLY!\n";
    echo "================================================================================\n\n";
    
    echo "📊 Result:\n";
    echo "   Message ID: " . ($result ?? 'N/A') . "\n\n";
    
    echo "💡 Check your browser for the notification!\n";
    echo "   - Look at bottom-right corner (Windows)\n";
    echo "   - Check notification center\n";
    echo "   - Check browser console for logs\n\n";
    
    echo "🔍 If notification doesn't appear:\n";
    echo "   1. Make sure browser tab is OPEN\n";
    echo "   2. Check browser console (F12) for errors\n";
    echo "   3. Check Windows notification settings\n";
    echo "   4. Try 'Test Manual Notification' button in fcm-debug.html\n\n";
    
    echo "================================================================================\n";

} catch (\Exception $e) {
    echo "\n";
    echo "================================================================================\n";
    echo "❌ ERROR SENDING NOTIFICATION\n";
    echo "================================================================================\n\n";
    
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n\n";
    
    if (strpos($e->getMessage(), 'not-found') !== false) {
        echo "💡 This error means the token is invalid or expired.\n";
        echo "   Solution:\n";
        echo "   1. Open: http://localhost:8000/fcm-debug.html\n";
        echo "   2. Click 'Initialize FCM' to get a fresh token\n";
        echo "   3. Run this script again with the new token\n\n";
    } elseif (strpos($e->getMessage(), 'invalid-argument') !== false) {
        echo "💡 This error means the token format is invalid.\n";
        echo "   Make sure you copied the complete token.\n\n";
    }
    
    echo "Full error:\n";
    echo $e->getTraceAsString() . "\n\n";
    
    exit(1);
}
