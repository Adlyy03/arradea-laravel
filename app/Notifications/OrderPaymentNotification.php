<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $event,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = match ($this->event) {
            'submitted' => 'Bukti pembayaran untuk ' . ($this->order->product?->name ?? 'produk') . ' sudah diupload dan menunggu konfirmasi.',
            'approved' => 'Pembayaran untuk ' . ($this->order->product?->name ?? 'produk') . ' sudah disetujui penjual.',
            'rejected' => 'Bukti pembayaran untuk ' . ($this->order->product?->name ?? 'produk') . ' ditolak penjual.',
            default => 'Status pembayaran pesanan Anda berubah.',
        };

        return [
            'type' => 'order_payment',
            'event' => $this->event,
            'order_id' => $this->order->id,
            'message' => $message,
            'url' => route('buyer.orders.show', $this->order->id),
        ];
    }
}