<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Format harga ke format Rupiah
     */
    public static function formatRupiah(float $price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /**
     * Hitung harga setelah diskon
     */
    public static function calculateDiscountedPrice(float $originalPrice, float $discountPercent): float
    {
        return max(0, $originalPrice * (1 - ($discountPercent / 100)));
    }

    /**
     * Hitung jumlah penghematan
     */
    public static function calculateSavings(float $originalPrice, float $finalPrice): float
    {
        return max(0, $originalPrice - $finalPrice);
    }

    /**
     * Format badge diskon
     */
    public static function formatDiscountBadge(float $discountPercent): string
    {
        return round($discountPercent) . '% OFF';
    }

    /**
     * Check apakah diskon sedang aktif berdasarkan tanggal
     */
    public static function isDiscountActive(
        ?string $startAt,
        ?string $endAt,
        float $discountPercent
    ): bool {
        if ($discountPercent <= 0) {
            return false;
        }

        $now = now('Asia/Jakarta');

        if ($startAt && $now->lt(\Carbon\Carbon::parse($startAt))) {
            return false;
        }

        if ($endAt && $now->gt(\Carbon\Carbon::parse($endAt))) {
            return false;
        }

        return true;
    }
}
