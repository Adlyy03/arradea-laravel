@props(['user'])

@php
$activeMode = $user->getActiveMode();
$canSwitchToSeller = $user->canSwitchToSellerMode();
@endphp

<div class="flex flex-col gap-2">

    {{-- ── Buyer Card ─────────────────────────────── --}}
    <form method="POST" action="{{ route('mode.switch') }}" class="m-0">
        @csrf
        <input type="hidden" name="mode" value="buyer">
        <button type="submit"
            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl border-2 transition-all duration-200 cursor-pointer hover:shadow-sm text-left"
            style="border-color:{{ $activeMode==='buyer' ? '#3b82f6' : '#f0f0f0' }};
                   background:{{ $activeMode==='buyer' ? '#eff6ff' : '#fafafa' }};">

            {{-- Icon --}}
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                style="background:{{ $activeMode==='buyer' ? '#3b82f6' : '#e5e7eb' }};">🛒</div>

            {{-- Label --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 leading-tight">Mode Buyer</span>
                    @if($activeMode==='buyer')
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded-full font-black text-white flex-shrink-0"
                            style="background:#3b82f6; line-height:1.4;">AKTIF</span>
                    @endif
                </div>
                <div class="text-xs text-gray-400 mt-0.5 leading-tight">Cari & beli produk favoritmu</div>
            </div>

            {{-- Check / Arrow --}}
            @if($activeMode==='buyer')
                <svg class="w-5 h-5 flex-shrink-0" style="color:#3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            @else
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            @endif
        </button>
    </form>

    {{-- ── Seller Card ─────────────────────────────── --}}
    @if($canSwitchToSeller)
        <form method="POST" action="{{ route('mode.switch') }}" class="m-0">
            @csrf
            <input type="hidden" name="mode" value="seller">
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-3 rounded-xl border-2 transition-all duration-200 cursor-pointer hover:shadow-sm text-left"
                style="border-color:{{ $activeMode==='seller' ? '#f59e0b' : '#f0f0f0' }};
                       background:{{ $activeMode==='seller' ? '#fffbeb' : '#fafafa' }};">

                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                    style="background:{{ $activeMode==='seller' ? '#f59e0b' : '#e5e7eb' }};">🏪</div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900 leading-tight">Mode Seller</span>
                        @if($activeMode==='seller')
                            <span class="inline-block text-[9px] px-1.5 py-0.5 rounded-full font-black text-white flex-shrink-0"
                                style="background:#f59e0b; line-height:1.4;">AKTIF</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5 leading-tight">Kelola toko & mulai jualan</div>
                </div>

                @if($activeMode==='seller')
                    <svg class="w-5 h-5 flex-shrink-0" style="color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </button>
        </form>
    @else
        {{-- Seller locked --}}
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 opacity-60">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl bg-gray-200 grayscale">🏪</div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-gray-400 leading-tight">Mode Seller</div>
                <div class="text-xs text-gray-400 mt-0.5 leading-tight">
                    @if($user->is_seller) Menunggu verifikasi admin @else Daftar seller dahulu @endif
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
        </div>
    @endif

</div>
