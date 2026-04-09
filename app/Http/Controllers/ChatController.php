<?php

namespace App\Http\Controllers;

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

        $recipientId = ($user->id === $chat->buyer_id) ? $chat->seller_id : $chat->buyer_id;
        $recipient = \App\Models\User::find($recipientId);
        if ($recipient) {
            $recipient->notify(new \App\Notifications\ChatMessageNotification($message));
        }

        return back();
    }

    /**
     * Get unread messages count for user.
     */
    public function unreadCount()
    {
        $user = auth()->user();

        if ($user->role === 'buyer') {
            $count = Message::whereHas('chat', function ($q) use ($user) {
                $q->where('buyer_id', $user->id);
            })->where('sender_id', '!=', $user->id)->where('is_read', false)->count();
        } else {
            $count = Message::whereHas('chat', function ($q) use ($user) {
                $q->where('seller_id', $user->id);
            })->where('sender_id', '!=', $user->id)->where('is_read', false)->count();
        }

        return response()->json(['count' => $count]);
    }
}
