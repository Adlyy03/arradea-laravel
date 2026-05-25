<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;

class PushNotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(User $user, string $title, string $body, ?array $data = [], ?string $icon = null, ?string $clickAction = null): array
    {
        $tokens = $user->fcmTokens()->active()->pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found for user',
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $icon, $clickAction);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $userIds, string $title, string $body, ?array $data = [], ?string $icon = null, ?string $clickAction = null): array
    {
        $tokens = FcmToken::whereIn('user_id', $userIds)
            ->active()
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found',
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $icon, $clickAction);
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(string $title, string $body, ?array $data = [], ?string $icon = null, ?string $clickAction = null): array
    {
        $tokens = FcmToken::active()->pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No active FCM tokens found',
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $icon, $clickAction);
    }

    /**
     * Send notification to specific tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, ?array $data = [], ?string $icon = null, ?string $clickAction = null): array
    {
        try {
            Log::info('='.str_repeat('=', 79));
            Log::info('📤 SENDING FCM NOTIFICATION');
            Log::info('='.str_repeat('=', 79));
            Log::info('Title: ' . $title);
            Log::info('Body: ' . $body);
            Log::info('Icon: ' . ($icon ?? 'null'));
            Log::info('Click Action: ' . ($clickAction ?? url('/')));
            Log::info('Data payload: ' . json_encode($data));
            Log::info('Number of tokens: ' . count($tokens));
            Log::info('Tokens: ' . json_encode($tokens));
            
            // Build notification
            $notification = Notification::create($title, $body);
            
            if ($icon) {
                $notification = $notification->withImageUrl($icon);
            }

            // Build data payload
            $dataPayload = array_merge($data, [
                'click_action' => $clickAction ?? url('/'),
                'timestamp' => now()->toIso8601String(),
            ]);

            Log::info('Final data payload: ' . json_encode($dataPayload));

            // Build message
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($dataPayload);

            Log::info('Message built successfully');
            Log::info('Sending to FCM...');

            // Send to multiple tokens
            $report = $this->messaging->sendMulticast($message, $tokens);

            Log::info('FCM Response:');
            Log::info('  Total: ' . count($tokens));
            Log::info('  Successful: ' . $report->successes()->count());
            Log::info('  Failed: ' . $report->failures()->count());

            // Handle invalid tokens
            $invalidTokens = [];
            if ($report->hasFailures()) {
                Log::warning('Some notifications failed:');
                foreach ($report->failures()->getItems() as $failure) {
                    $token = $failure->target()->value();
                    $invalidTokens[] = $token;
                    Log::warning('  Failed token: ' . substr($token, 0, 20) . '...');
                    Log::warning('  Error: ' . $failure->error()->getMessage());
                }

                // Deactivate invalid tokens
                if (!empty($invalidTokens)) {
                    FcmToken::whereIn('token', $invalidTokens)->update(['is_active' => false]);
                    Log::info('Deactivated ' . count($invalidTokens) . ' invalid tokens');
                }
            }

            Log::info('✅ Notification sent successfully');
            Log::info('='.str_repeat('=', 79));

            return [
                'success' => true,
                'total' => count($tokens),
                'successful' => $report->successes()->count(),
                'failed' => $report->failures()->count(),
                'invalid_tokens' => $invalidTokens,
            ];

        } catch (\Exception $e) {
            Log::error('='.str_repeat('=', 79));
            Log::error('❌ PUSH NOTIFICATION ERROR');
            Log::error('='.str_repeat('=', 79));
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            Log::error('Error trace: ' . $e->getTraceAsString());
            Log::error('='.str_repeat('=', 79));

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, ?array $data = [], ?string $icon = null, ?string $clickAction = null): array
    {
        try {
            $notification = Notification::create($title, $body);
            
            if ($icon) {
                $notification = $notification->withImageUrl($icon);
            }

            $dataPayload = array_merge($data, [
                'click_action' => $clickAction ?? url('/'),
                'timestamp' => now()->toIso8601String(),
            ]);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($dataPayload)
                ->withTopic($topic);

            $this->messaging->send($message);

            return [
                'success' => true,
                'message' => "Notification sent to topic: {$topic}",
            ];

        } catch (\Exception $e) {
            Log::error('Push notification to topic error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Subscribe tokens to a topic
     */
    public function subscribeToTopic(array $tokens, string $topic): array
    {
        try {
            $this->messaging->subscribeToTopic($topic, $tokens);

            return [
                'success' => true,
                'message' => "Subscribed to topic: {$topic}",
            ];

        } catch (\Exception $e) {
            Log::error('Subscribe to topic error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Unsubscribe tokens from a topic
     */
    public function unsubscribeFromTopic(array $tokens, string $topic): array
    {
        try {
            $this->messaging->unsubscribeFromTopic($topic, $tokens);

            return [
                'success' => true,
                'message' => "Unsubscribed from topic: {$topic}",
            ];

        } catch (\Exception $e) {
            Log::error('Unsubscribe from topic error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
