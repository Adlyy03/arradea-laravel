<?php

namespace App\Observers;

use App\Events\ProductUpdated;
use App\Models\Product;
use App\Services\CacheService;

class ProductObserver
{
    public function created(Product $product): void
    {
        // Clear cache
        CacheService::clearProductCache();
        
        $this->dispatchProductUpdated($product);
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['name', 'price', 'stock', 'image', 'updated_at'])) {
            // Clear cache
            CacheService::clearProductCache($product->id);
            
            $this->dispatchProductUpdated($product);
        }
    }

    public function deleted(Product $product): void
    {
        // Clear cache
        CacheService::clearProductCache($product->id);
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
