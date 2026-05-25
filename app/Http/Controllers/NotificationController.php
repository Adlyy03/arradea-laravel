<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Save FCM token to user
     */
    public function saveFCMToken(Request $request)
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Get device info from request
            $deviceName = $request->input('device_name', 'Web Browser');
            $deviceType = $request->input('device_type', 'web');

            // IMPORTANT: Deactivate ALL old tokens for this user
            // This ensures we only send to the latest token
            \App\Models\FcmToken::where('user_id', $user->id)
                ->where('token', '!=', $request->fcm_token)
                ->update(['is_active' => false]);

            Log::info('Deactivated old FCM tokens for user', [
                'user_id' => $user->id
            ]);

            // Check if token already exists for this user
            $existingToken = \App\Models\FcmToken::where('user_id', $user->id)
                ->where('token', $request->fcm_token)
                ->first();

            if ($existingToken) {
                // Update existing token
                $existingToken->update([
                    'device_name' => $deviceName,
                    'device_type' => $deviceType,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                Log::info('FCM token updated', [
                    'user_id' => $user->id,
                    'token_id' => $existingToken->id,
                    'token' => substr($request->fcm_token, 0, 20) . '...'
                ]);
            } else {
                // Create new token
                $fcmToken = \App\Models\FcmToken::create([
                    'user_id' => $user->id,
                    'token' => $request->fcm_token,
                    'device_name' => $deviceName,
                    'device_type' => $deviceType,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                Log::info('FCM token created', [
                    'user_id' => $user->id,
                    'token_id' => $fcmToken->id,
                    'token' => substr($request->fcm_token, 0, 20) . '...'
                ]);
            }

            // Also save to users.fcm_token for backward compatibility
            $user->update([
                'fcm_token' => $request->fcm_token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving FCM token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan FCM token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send push notification to user using Firebase Cloud Messaging
     * 
     * @param int|array $userIds User ID or array of user IDs
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param string|null $image Notification image URL
     * @return array Response with success status and details
     */
    public static function sendPushNotification($userIds, string $title, string $body, array $data = [], ?string $image = null): array
    {
        try {
            // Convert single user ID to array
            if (!is_array($userIds)) {
                $userIds = [$userIds];
            }

            // Get active FCM tokens for users
            $fcmTokens = \App\Models\FcmToken::whereIn('user_id', $userIds)
                ->where('is_active', true)
                ->pluck('token')
                ->toArray();

            if (empty($fcmTokens)) {
                Log::warning('No active FCM tokens found', ['user_ids' => $userIds]);
                return [
                    'success' => false,
                    'message' => 'Tidak ada user dengan FCM token aktif',
                    'sent_count' => 0
                ];
            }

            // Update last_used_at for these tokens
            \App\Models\FcmToken::whereIn('user_id', $userIds)
                ->where('is_active', true)
                ->update(['last_used_at' => now()]);
            $serverKey = env('FCM_SERVER_KEY');

            if (!$serverKey) {
                Log::error('FCM_SERVER_KEY not configured in .env');
                return [
                    'success' => false,
                    'message' => 'FCM Server Key tidak dikonfigurasi',
                    'sent_count' => 0
                ];
            }

            // Prepare notification payload
            $notification = [
                'title' => $title,
                'body' => $body,
                'icon' => asset('images/logo.png'),
                'badge' => asset('images/badge.png'),
            ];

            if ($image) {
                $notification['image'] = $image;
            }

            // Prepare FCM payload
            $payload = [
                'registration_ids' => $fcmTokens,
                'notification' => $notification,
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => 'default',
                ]),
                'priority' => 'high',
                'content_available' => true,
            ];

            // Send to FCM
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            $responseData = $response->json();

            if ($response->successful()) {
                $successCount = $responseData['success'] ?? 0;
                $failureCount = $responseData['failure'] ?? 0;

                Log::info('Push notification sent', [
                    'title' => $title,
                    'recipients' => count($fcmTokens),
                    'success' => $successCount,
                    'failure' => $failureCount
                ]);

                return [
                    'success' => true,
                    'message' => 'Notifikasi berhasil dikirim',
                    'sent_count' => $successCount,
                    'failed_count' => $failureCount,
                    'response' => $responseData
                ];
            } else {
                Log::error('FCM request failed', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim notifikasi',
                    'sent_count' => 0,
                    'error' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error sending push notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'sent_count' => 0
            ];
        }
    }

    /**
     * Example: Send notification to seller when new order is created
     */
    public static function notifyNewOrder($order)
    {
        $seller = $order->store->user;
        
        return self::sendPushNotification(
            $seller->id,
            '🛒 Pesanan Baru!',
            "Kamu mendapat pesanan baru dari {$order->user->name}",
            [
                'type' => 'new_order',
                'order_id' => $order->id,
                'url' => route('seller.orders')
            ],
            $order->product->image ? asset('storage/' . $order->product->image) : null
        );
    }

    /**
     * Example: Send notification to buyer when order status changes
     */
    public static function notifyOrderStatusChange($order, string $status)
    {
        $statusMessages = [
            'processing' => '✅ Pesanan Diterima',
            'completed' => '🎉 Pesanan Selesai',
            'cancelled' => '❌ Pesanan Dibatalkan',
        ];

        $title = $statusMessages[$status] ?? 'Status Pesanan Berubah';
        $body = "Pesanan #{$order->id} - {$order->product->name}";

        return self::sendPushNotification(
            $order->user_id,
            $title,
            $body,
            [
                'type' => 'order_status_change',
                'order_id' => $order->id,
                'status' => $status,
                'url' => route('buyer.orders.show', $order->id)
            ],
            $order->product->image ? asset('storage/' . $order->product->image) : null
        );
    }

    /**
     * Example: Send notification when payment is approved/rejected
     */
    public static function notifyPaymentStatus($order, bool $approved)
    {
        $title = $approved ? '✅ Pembayaran Diterima' : '❌ Pembayaran Ditolak';
        $body = $approved 
            ? "Pembayaran untuk pesanan #{$order->id} telah diterima"
            : "Pembayaran untuk pesanan #{$order->id} ditolak. Silakan upload ulang bukti pembayaran.";

        return self::sendPushNotification(
            $order->user_id,
            $title,
            $body,
            [
                'type' => 'payment_status',
                'order_id' => $order->id,
                'approved' => $approved,
                'url' => route('buyer.orders.show', $order->id)
            ]
        );
    }

    /**
     * Example: Send notification for new chat message
     */
    public static function notifyChatMessage($message, $recipientId)
    {
        $sender = $message->user;
        
        return self::sendPushNotification(
            $recipientId,
            "💬 Pesan dari {$sender->name}",
            $message->message,
            [
                'type' => 'chat_message',
                'chat_id' => $message->chat_id,
                'message_id' => $message->id,
                'url' => route('seller.messages') // or buyer messages route
            ]
        );
    }
}
