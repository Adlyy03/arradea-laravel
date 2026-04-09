<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'order_id',
        'buyer_id',
        'seller_id',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Chat belongs to an order */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** Chat belongs to buyer */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /** Chat belongs to seller */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /** Chat has many messages */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
