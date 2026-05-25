<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $pushNotification;

    public function __construct(PushNotificationService $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    /**
     * Show chat for an order.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        // Ensure user is buyer or seller of this order
        if (!($user->id === $order->user_id || ($user->store && $user->store->id === $order->store_id))) {
            abort(403);
        }

        $chat = $order->chat;

        if (!$chat) {
            $chat = Chat::create([
                'order_id' => $order->id,
                'buyer_id' => $order->user_id,
                'seller_id' => $order->store->user_id,
            ]);
        }

        $messages = $chat->messages()->with('sender')->orderBy('created_at')->get();

        // Mark messages as read for current user
        $chat->messages()->where('sender_id', '!=', $user->id)->update(['is_read' => true]);

        return view('chat.show', compact('order', 'chat', 'messages'));
    }

    /**
     * Send a message.
     */
    public function store(Request $request, Chat $chat)
    {
        $user = auth()->user();

        // Ensure user is part of this chat
        if ($user->id !== $chat->buyer_id && $user->id !== $chat->seller_id) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'message'   => $request->message,
        ]);

        $message->load('sender:id,name');

        try {
            event(new MessageSent($message));
        } catch (\Throwable $exception) {
            report($exception);
        }

        $recipientId = ($user->id === $chat->buyer_id) ? $chat->seller_id : $chat->buyer_id;
        $recipient = \App\Models\User::find($recipientId);
        
        \Log::info('='.str_repeat('=', 79));
        \Log::info('💬 CHAT MESSAGE - Attempting to send notification');
        \Log::info('Sender: ' . $user->name . ' (ID: ' . $user->id . ')');
        \Log::info('Recipient: ' . ($recipient ? $recipient->name . ' (ID: ' . $recipient->id . ')' : 'NOT FOUND'));
        \Log::info('Message: ' . $request->message);
        \Log::info('Chat ID: ' . $chat->id);
        
        if ($recipient) {
            \Log::info('Recipient found, checking FCM tokens...');
            $tokenCount = $recipient->fcmTokens()->active()->count();
            \Log::info('Active FCM tokens: ' . $tokenCount);
            
            try {
                $recipient->notify(new \App\Notifications\ChatMessageNotification($message));
                \Log::info('✅ Email/Database notification sent');
                
                // Send Push Notification
                $order = $chat->order;
                $messagePreview = strlen($request->message) > 50 
                    ? substr($request->message, 0, 50) . '...' 
                    : $request->message;
                
                \Log::info('Sending push notification...');
                \Log::info('Title: 💬 Pesan dari ' . $user->name);
                \Log::info('Body: ' . $messagePreview);
                
                $result = $this->pushNotification->sendToUser(
                    $recipient,
                    "💬 Pesan dari {$user->name}",
                    $messagePreview,
                    [
                        'type' => 'chat_message',
                        'chat_id' => (string)$chat->id,
                        'order_id' => (string)$order->id,
                        'sender_id' => (string)$user->id,
                        'sender_name' => $user->name
                    ],
                    asset('icons/logo-arradea.png'),
                    url('/chat/' . $order->id)
                );
                
                \Log::info('Push notification result: ' . json_encode($result));
                \Log::info('='.str_repeat('=', 79));
            } catch (\Throwable $exception) {
                \Log::error('❌ Error sending chat notification: ' . $exception->getMessage());
                \Log::error('Stack trace: ' . $exception->getTraceAsString());
                report($exception);
            }
        } else {
            \Log::warning('⚠️ Recipient not found!');
            \Log::info('='.str_repeat('=', 79));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toIso8601String(),
                ],
            ], 201);
        }

        return back();
    }

    /**
     * Get unread messages count for user.
     */
    public function unreadCount()
    {
        $user = auth()->user();

        $count = Message::whereHas('chat', function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
