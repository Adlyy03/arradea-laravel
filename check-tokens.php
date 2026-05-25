<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "================================================================================\n";
echo "CHECKING FCM TOKENS IN DATABASE\n";
echo "================================================================================\n\n";

$tokens = \App\Models\FcmToken::orderBy('id', 'desc')->get();

echo "Total tokens: " . $tokens->count() . "\n\n";

foreach ($tokens as $token) {
    echo "ID: {$token->id}\n";
    echo "User ID: {$token->user_id}\n";
    echo "Token: " . substr($token->token, 0, 50) . "...\n";
    echo "Active: " . ($token->is_active ? 'YES' : 'NO') . "\n";
    echo "Created: {$token->created_at}\n";
    echo "Last used: {$token->last_used_at}\n";
    echo str_repeat('-', 80) . "\n";
}

echo "\n";
echo "ACTIVE TOKENS ONLY:\n";
echo "================================================================================\n\n";

$activeTokens = \App\Models\FcmToken::where('is_active', true)->get();

echo "Active tokens: " . $activeTokens->count() . "\n\n";

foreach ($activeTokens as $token) {
    echo "ID: {$token->id} | User: {$token->user_id}\n";
    echo "Token: {$token->token}\n";
    echo str_repeat('-', 80) . "\n";
}
