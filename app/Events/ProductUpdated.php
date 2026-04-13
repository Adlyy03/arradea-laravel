<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Product $product)
    {
        $this->product->loadMissing('store:id,name', 'category:id,name');
    }

    public function broadcastOn(): array
    {
        return [new Channel('products')];
    }

    public function broadcastAs(): string
    {
        return 'ProductUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'price' => (float) $this->product->price,
            'stock' => (int) $this->product->stock,
            'status' => $this->product->stock > 0 ? 'available' : 'out_of_stock',
            'image' => $this->product->image,
            'store_id' => $this->product->store_id,
            'store_name' => $this->product->store?->name,
            'category_name' => $this->product->category?->name,
            'updated_at' => $this->product->updated_at?->toIso8601String(),
        ];
    }
}
