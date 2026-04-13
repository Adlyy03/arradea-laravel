<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'wilayah',
        'access_code_id',
        'password',
        'is_seller',
        'role',
        'seller_status',
        'seller_applied_at',
        'seller_approved_at',
        'seller_rejected_at',
        'seller_rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_seller'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function isSeller(): bool
    {
        return (bool) $this->is_seller;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** A seller has one store */
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    /** A buyer has many orders */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** A buyer has many cart items */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function accessCode()
    {
        return $this->belongsTo(AccessCode::class);
    }
}
