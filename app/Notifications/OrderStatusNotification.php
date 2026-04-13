<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = [
            'pending' => 'Menunggu Dikonfirmasi',
            'accepted' => 'Diproses',
            'rejected' => 'Ditolak',
            'done' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ][$this->order->status] ?? $this->order->status;

        return [
            'type' => 'order_status',
            'order_id' => $this->order->id,
            'message' => 'Status pesanan Anda untuk ' . ($this->order->product ? $this->order->product->name : 'Produk') . ' sekarang: ' . $statusLabel,
            'url' => route('buyer.orders.show', $this->order->id)
        ];
    }
}
