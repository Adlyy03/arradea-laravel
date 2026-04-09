<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $chatMessage;

    public function __construct(Message $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $senderName = $this->chatMessage->sender ? $this->chatMessage->sender->name : 'Pengguna';
        return [
            'type' => 'new_message',
            'chat_id' => $this->chatMessage->chat_id,
            'message' => 'Pesan baru dari ' . $senderName . ': ' . \Illuminate\Support\Str::limit($this->chatMessage->message, 30),
            'url' => route('chat.show', $this->chatMessage->chat ? $this->chatMessage->chat->order_id : '')
        ];
    }
}
