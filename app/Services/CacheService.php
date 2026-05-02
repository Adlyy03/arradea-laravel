<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache TTL constants (in seconds)
    const TTL_PRODUCTS = 300; // 5 minutes
    const TTL_CATEGORIES = 600; // 10 minutes
    const TTL_STORES = 300; // 5 minutes
    const TTL_FEATURED = 600; // 10 minutes

    /**
     * Get cached products list
     */
    public static function getProducts(int $page = 1)
    {
        return Cache::remember("products:page:{$page}", self::TTL_PRODUCTS, function () {
            return \App\Models\Product::with(['store:id,name,address', 'category:id,name'])
                ->whereHas('store.user', function ($q) {
                    $q->where('is_seller', true);
                })
                ->whereHas('store', function ($q) {
                    $q->where('status', 'active');
                })
                ->latest()
                ->paginate(20);
        });
    }

    /**
     * Get cached categories
     */
    public static function getCategories()
    {
        return Cache::remember('categories:all', self::TTL_CATEGORIES, function () {
            return \App\Models\Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached featured categories
     */
    public static function getFeaturedCategories()
    {
        return Cache::remember('categories:featured', self::TTL_FEATURED, function () {
            return \App\Models\Category::where('is_featured', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get cached product by ID
     */
    public static function getProduct(int $id)
    {
        return Cache::remember("product:{$id}", self::TTL_PRODUCTS, function () use ($id) {
            return \App\Models\Product::with(['store', 'category'])
                ->find($id);
        });
    }

    /**
     * Clear product cache
     */
    public static function clearProductCache(?int $productId = null)
    {
        if ($productId) {
            Cache::forget("product:{$productId}");
        }
        
        // Clear paginated product lists
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("products:page:{$page}");
        }
        
        Cache::forget('products:featured');
        Cache::forget('products:discounted');
        Cache::forget('products:popular');
    }

    /**
     * Clear category cache
     */
    public static function clearCategoryCache()
    {
        Cache::forget('categories:all');
        Cache::forget('categories:featured');
        
        // Clear category-specific caches
        $categories = \App\Models\Category::pluck('id');
        foreach ($categories as $id) {
            Cache::forget("category:{$id}:products");
        }
    }

    /**
     * Clear store cache
     */
    public static function clearStoreCache(int $storeId)
    {
        Cache::forget("store:{$storeId}");
        Cache::forget("store:{$storeId}:products");
        Cache::forget("store:{$storeId}:orders");
    }

    /**
     * Clear all cache
     */
    public static function clearAll()
    {
        Cache::flush();
    }
}
