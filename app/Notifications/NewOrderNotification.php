<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
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
        $this->order->loadMissing('user:id,name', 'product:id,name');

        return [
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'buyer_name' => $this->order->user?->name ?? 'Pembeli',
            'total_price' => (float) $this->order->total_price,
            'ordered_at' => optional($this->order->created_at)->toDateTimeString(),
            'product_name' => $this->order->product?->name,
            'message' => 'Pesanan baru masuk dari ' . ($this->order->user?->name ?? 'Pembeli'),
            'url' => route('seller.orders'),
        ];
    }
}
