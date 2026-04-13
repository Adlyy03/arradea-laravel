<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'product_id',
        'variant_key',
        'quantity',
        'unit_price_original',
        'unit_price_final',
        'discount_percent_applied',
        'total_price',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'float',
            'unit_price_original' => 'float',
            'unit_price_final' => 'float',
            'discount_percent_applied' => 'float',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Order belongs to a buyer */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Order belongs to a store */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /** Order belongs to a specific product */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Order has one chat */
    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}
