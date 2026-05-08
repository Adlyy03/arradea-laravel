@props(['user'])

@php
$activeMode = $user->getActiveMode();
$canSwitchToSeller = $user->canSwitchToSellerMode();
@endphp

<!-- Bottom Sheet Overlay -->
<div 
    x-data="{ open: false }" 
    @keydown.escape.window="open = false"
    class="mode-switcher-container"
>
    <!-- Trigger Button -->
    <button 
        @click="open = true"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
        aria-label="Ganti Mode"
    >
        <x-mode-badge :mode="$activeMode" />
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
        </svg>
    </button>

    <!-- Bottom Sheet -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        style="display: none;"
    ></div>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-50 max-h-[80vh] overflow-y-auto"
        style="display: none;"
        @click.away="open = false"
    >
        <!-- Handle Bar -->
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pilih Mode</h3>
            <p class="text-sm text-gray-500 mt-1">Ganti antara mode Buyer dan Seller</p>
        </div>

        <!-- Mode Options -->
        <div class="p-6 space-y-3">
            <!-- Buyer Mode -->
            <form method="POST" action="{{ route('mode.switch') }}" class="mode-option-form">
                @csrf
                <input type="hidden" name="mode" value="buyer">
                <button 
                    type="submit"
                    class="w-full flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-md {{ $activeMode === 'buyer' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}"
                    style="min-height: 44px;"
                >
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full {{ $activeMode === 'buyer' ? 'bg-blue-100' : 'bg-gray-100' }}">
                        <span class="text-2xl">🛒</span>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                            Mode Buyer
                            @if($activeMode === 'buyer')
                                <span class="text-xs px-2 py-0.5 bg-blue-500 text-white rounded-full">Aktif</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">Belanja produk dari seller</div>
                    </div>
                    @if($activeMode === 'buyer')
                        <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            </form>

            <!-- Seller Mode -->
            @if($canSwitchToSeller)
                <form method="POST" action="{{ route('mode.switch') }}" class="mode-option-form">
                    @csrf
                    <input type="hidden" name="mode" value="seller">
                    <button 
                        type="submit"
                        class="w-full flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-md {{ $activeMode === 'seller' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}"
                        style="min-height: 44px;"
                    >
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full {{ $activeMode === 'seller' ? 'bg-purple-100' : 'bg-gray-100' }}">
                            <span class="text-2xl">🏪</span>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                Mode Seller
                                @if($activeMode === 'seller')
                                    <span class="text-xs px-2 py-0.5 bg-purple-500 text-white rounded-full">Aktif</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mt-0.5">Kelola toko dan produk</div>
                        </div>
                        @if($activeMode === 'seller')
                            <svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </button>
                </form>
            @else
                <div class="w-full flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed" style="min-height: 44px;">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-gray-200">
                        <span class="text-2xl grayscale">🏪</span>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="font-semibold text-gray-500">Mode Seller</div>
                        <div class="text-sm text-gray-400 mt-0.5">
                            @if($user->is_seller)
                                Menunggu persetujuan admin
                            @else
                                Daftar sebagai seller terlebih dahulu
                            @endif
                        </div>
                    </div>
                    <svg class="w-6 h-6 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <button 
                @click="open = false"
                type="button"
                class="w-full px-4 py-3 text-center font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                style="min-height: 44px;"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
/* Smooth animations for bottom sheet */
.mode-switcher-container {
    -webkit-tap-highlight-color: transparent;
}

.mode-option-form button:active {
    transform: scale(0.98);
}

/* Haptic feedback simulation */
@keyframes haptic-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(0.98); }
}

.mode-option-form button:active {
    animation: haptic-pulse 0.15s ease-in-out;
}
</style>
