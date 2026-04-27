<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
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
        if ($recipient) {
            try {
                $recipient->notify(new \App\Notifications\ChatMessageNotification($message));
            } catch (\Throwable $exception) {
                report($exception);
            }
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
