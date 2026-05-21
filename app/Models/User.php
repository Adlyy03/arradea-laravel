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
        'phone',
        'wilayah',
        'latitude',
        'longitude',
        'access_code_id',
        'password',
        'phone_verified_at',
        'is_seller',
        'role',
        'seller_status',
        'seller_applied_at',
        'seller_approved_at',
        'seller_rejected_at',
        'seller_rejection_reason',
        'seller_otp_verified',
        'store_status',
        'open_time',
        'close_time',
        'auto_schedule',
        'preferred_mode',
        'qris_image',
        'payment_name',
        'payment_type',
        'payment_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified_at'  => 'datetime',
            'latitude'           => 'float',
            'longitude'          => 'float',
            'is_seller'          => 'boolean',
            'seller_otp_verified' => 'boolean',
            'store_status'       => 'string',
            'open_time'          => 'string',
            'close_time'         => 'string',
            'auto_schedule'      => 'boolean',
            'seller_applied_at'  => 'datetime',
            'seller_rejected_at' => 'datetime',
            'seller_approved_at' => 'datetime',
            'payment_name'       => 'string',
            'payment_type'       => 'string',
            'payment_number'     => 'string',
            'qris_image'         => 'string',
            'password'           => 'hashed',
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

    /** A user has many FCM tokens */
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function accessCode()
    {
        return $this->belongsTo(AccessCode::class);
    }

    public function username()
    {
        return 'phone';
    }

    // ─── Mode Switching Methods ─────────────────────────────────────────────────

    /**
     * Get the current active mode from session.
     * Falls back to preferred_mode if session is empty.
     */
    public function getActiveMode(): string
    {
        return session('active_mode', $this->preferred_mode ?? 'buyer');
    }

    /**
     * Set the active mode in session and update preferred_mode in DB.
     */
    public function setActiveMode(string $mode): void
    {
        if (!in_array($mode, ['buyer', 'seller'])) {
            throw new \InvalidArgumentException("Mode tidak valid: {$mode}");
        }

        session(['active_mode' => $mode]);
        $this->update(['preferred_mode' => $mode]);
    }

    /**
     * Check if user is currently in seller mode.
     */
    public function isInSellerMode(): bool
    {
        return $this->getActiveMode() === 'seller';
    }

    /**
     * Check if user can switch to seller mode.
     */
    public function canSwitchToSellerMode(): bool
    {
        return $this->is_seller && $this->seller_status === 'approved';
    }

    public function hasQrisPaymentSetup(): bool
    {
        return filled($this->qris_image) && filled($this->payment_name);
    }
}
