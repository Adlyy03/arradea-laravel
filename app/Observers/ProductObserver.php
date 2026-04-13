<?php

namespace App\Observers;

use App\Events\ProductUpdated;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->dispatchProductUpdated($product);
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['name', 'price', 'stock', 'image', 'updated_at'])) {
            $this->dispatchProductUpdated($product);
        }
    }

    protected function dispatchProductUpdated(Product $product): void
    {
        try {
            event(new ProductUpdated($product->fresh()));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
