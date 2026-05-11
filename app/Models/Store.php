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
        'store_status',
        'open_time',
        'close_time',
        'auto_schedule',
        'approved_at',
    ];

    protected $casts = [
        'auto_schedule' => 'boolean',
        'approved_at' => 'datetime',
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

    // ─── Helper Methods ─────────────────────────────────────────────────────────

    /**
     * Check if store is currently open
     */
    public function isOpen(): bool
    {
        return $this->store_status === 'open';
    }

    /**
     * Check if store is currently closed
     */
    public function isClosed(): bool
    {
        return $this->store_status !== 'open';
    }
}
