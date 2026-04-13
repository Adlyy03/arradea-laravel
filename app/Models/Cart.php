<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'variant_key',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Cart belongs to a user (buyer) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Cart belongs to a product */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
