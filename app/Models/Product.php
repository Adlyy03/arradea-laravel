<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'description',
        'price',
        'discount_percent',
        'discount_start_at',
        'discount_end_at',
        'stock',
        'image',
        'variants',
    ];

    protected $casts = [
        'price' => 'float',
        'discount_percent' => 'float',
        'discount_start_at' => 'datetime',
        'discount_end_at' => 'datetime',
        'stock' => 'integer',
        'variants' => 'array',
    ];

    // Default image fallback
    public function getImageAttribute($value)
    {
        return $value ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&h=500';
    }

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Product belongs to a store */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /** Product belongs to a category */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Product has many orders */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get a variant configuration from JSON by key.
     */
    public function getVariant(?string $variantKey): ?array
    {
        if (! $variantKey || $variantKey === 'default') {
            return null;
        }

        $variants = $this->variants ?? [];

        foreach ($variants as $variant) {
            if (($variant['key'] ?? null) === $variantKey) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * Resolve discount percent (variant-level takes precedence over product-level).
     */
    public function getActiveDiscountPercent(?string $variantKey = null, ?\Carbon\CarbonInterface $at = null): float
    {
        $at = $at ?: now();

        $variant = $this->getVariant($variantKey);
        if ($variant) {
            $variantPercent = (float) ($variant['discount_percent'] ?? 0);
            $variantStart = ! empty($variant['discount_start_at']) ? \Carbon\Carbon::parse($variant['discount_start_at']) : null;
            $variantEnd = ! empty($variant['discount_end_at']) ? \Carbon\Carbon::parse($variant['discount_end_at']) : null;

            if ($variantPercent > 0
                && (! $variantStart || $at->greaterThanOrEqualTo($variantStart))
                && (! $variantEnd || $at->lessThanOrEqualTo($variantEnd))) {
                return min(100, $variantPercent);
            }
        }

        $productPercent = (float) ($this->discount_percent ?? 0);
        if ($productPercent > 0
            && (! $this->discount_start_at || $at->greaterThanOrEqualTo($this->discount_start_at))
            && (! $this->discount_end_at || $at->lessThanOrEqualTo($this->discount_end_at))) {
            return min(100, $productPercent);
        }

        return 0;
    }

    /**
     * Compute original/final unit and total prices for checkout.
     */
    public function calculatePricing(?string $variantKey = null, int $quantity = 1): array
    {
        $variant = $this->getVariant($variantKey);
        $unitOriginal = (float) ($variant['price'] ?? $this->price);
        $discountPercent = $this->getActiveDiscountPercent($variantKey);
        $unitFinal = max(0, $unitOriginal * (1 - ($discountPercent / 100)));

        return [
            'unit_original' => $unitOriginal,
            'unit_final' => $unitFinal,
            'discount_percent' => $discountPercent,
            'total_original' => $unitOriginal * $quantity,
            'total_final' => $unitFinal * $quantity,
            'variant' => $variant,
        ];
    }
}
