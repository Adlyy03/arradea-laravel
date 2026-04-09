<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'status',
        'approved_at',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Store belongs to a seller (user) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Store has many products */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** Store has many orders */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
