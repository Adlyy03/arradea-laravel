<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['phone', 'code', 'expires_at'];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public static function generateCode(): string
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForPhone(string $phone): self
    {
        // Invalidate previous OTPs
        self::where('phone', $phone)
            ->where('verified_at', null)
            ->delete();

        return self::create([
            'phone'      => $phone,
            'code'       => self::generateCode(),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function verify(string $code): bool
    {
        if ($this->isExpired() || $this->isVerified()) {
            return false;
        }

        if ($this->code !== $code) {
            $this->increment('attempts');
            return false;
        }

        $this->update(['verified_at' => now()]);
        return true;
    }
}
