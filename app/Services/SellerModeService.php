<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class SellerModeService
{
    /**
     * Switch user mode between buyer and seller.
     *
     * @param User $user
     * @param string $targetMode 'buyer' or 'seller'
     * @return array ['success' => bool, 'message' => string, 'mode' => string]
     */
    public function switchMode(User $user, string $targetMode): array
    {
        // Validate target mode
        if (!in_array($targetMode, ['buyer', 'seller'])) {
            return [
                'success' => false,
                'message' => 'Mode tidak valid. Pilih buyer atau seller.',
                'mode' => $user->getActiveMode(),
            ];
        }

        // Check if already in target mode
        if ($user->getActiveMode() === $targetMode) {
            return [
                'success' => true,
                'message' => "Anda sudah dalam mode {$targetMode}.",
                'mode' => $targetMode,
            ];
        }

        // Check if user can switch to seller mode
        if ($targetMode === 'seller' && !$user->canSwitchToSellerMode()) {
            return [
                'success' => false,
                'message' => 'Anda belum memiliki akses seller atau belum disetujui admin.',
                'mode' => $user->getActiveMode(),
            ];
        }

        // Perform the switch
        try {
            $user->setActiveMode($targetMode);

            Log::info('User switched mode', [
                'user_id' => $user->id,
                'from' => $user->getActiveMode() === 'seller' ? 'buyer' : 'seller',
                'to' => $targetMode,
            ]);

            return [
                'success' => true,
                'message' => $targetMode === 'seller' 
                    ? '🎉 Selamat berjualan di Arradea!' 
                    : '🛒 Selamat berbelanja di Arradea!',
                'mode' => $targetMode,
            ];
        } catch (\Exception $e) {
            Log::error('Mode switch failed', [
                'user_id' => $user->id,
                'target_mode' => $targetMode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengganti mode. Silakan coba lagi.',
                'mode' => $user->getActiveMode(),
            ];
        }
    }

    /**
     * Get current mode info for user.
     *
     * @param User $user
     * @return array
     */
    public function getModeInfo(User $user): array
    {
        $activeMode = $user->getActiveMode();

        return [
            'active_mode' => $activeMode,
            'is_seller' => $user->is_seller,
            'can_switch_to_seller' => $user->canSwitchToSellerMode(),
            'seller_status' => $user->seller_status,
            'available_modes' => $this->getAvailableModes($user),
        ];
    }

    /**
     * Get list of available modes for user.
     *
     * @param User $user
     * @return array
     */
    protected function getAvailableModes(User $user): array
    {
        $modes = [
            [
                'value' => 'buyer',
                'label' => 'Mode Buyer',
                'icon' => '🛒',
                'description' => 'Belanja produk dari seller',
                'available' => true,
            ],
        ];

        if ($user->canSwitchToSellerMode()) {
            $modes[] = [
                'value' => 'seller',
                'label' => 'Mode Seller',
                'icon' => '🏪',
                'description' => 'Kelola toko dan produk',
                'available' => true,
            ];
        } else {
            $modes[] = [
                'value' => 'seller',
                'label' => 'Mode Seller',
                'icon' => '🏪',
                'description' => $user->is_seller 
                    ? 'Menunggu persetujuan admin' 
                    : 'Daftar sebagai seller terlebih dahulu',
                'available' => false,
            ];
        }

        return $modes;
    }

    /**
     * Initialize mode on login.
     *
     * @param User $user
     * @return string The initialized mode
     */
    public function initializeModeOnLogin(User $user): string
    {
        $preferredMode = $user->preferred_mode ?? 'buyer';

        // If preferred mode is seller but user can't access it, fallback to buyer
        if ($preferredMode === 'seller' && !$user->canSwitchToSellerMode()) {
            $preferredMode = 'buyer';
        }

        session(['active_mode' => $preferredMode]);

        return $preferredMode;
    }
}
