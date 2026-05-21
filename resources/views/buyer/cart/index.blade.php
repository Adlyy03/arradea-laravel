@extends('layouts.dashboard')
@section('title', 'Keranjang Belanja — Arradea')
@section('page_title', 'Keranjang')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 fade-up">
    @php
        $singleStoreIds = $carts->pluck('product.store_id')->unique();
        $singleStore = $singleStoreIds->count() === 1 ? $carts->first()?->product?->store : null;
        $singleSeller = $singleStore?->user;
        $hasQrisSeller = $singleSeller && $singleSeller->hasQrisPaymentSetup();
        
        // Debug info
        $debugInfo = [
            'has_cart' => $carts->isNotEmpty(),
            'single_store' => $singleStoreIds->count() === 1,
            'store_id' => $singleStore?->id,
            'seller_id' => $singleSeller?->id,
            'seller_name' => $singleSeller?->name,
            'has_qris_image' => filled($singleSeller?->qris_image ?? null),
            'has_payment_name' => filled($singleSeller?->payment_name ?? null),
            'qris_image_path' => $singleSeller?->qris_image,
            'payment_name' => $singleSeller?->payment_name,
            'hasQrisSeller' => $hasQrisSeller,
        ];
    @endphp

    <!-- {{-- Debug Info (hanya untuk development) --}}
    @if(config('app.debug'))
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <p class="font-black text-blue-900 text-xs mb-2">🔍 DEBUG INFO:</p>
        <pre class="text-[10px] text-blue-700 overflow-auto">{{ json_encode($debugInfo, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif -->

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl">❌</span>
            <div class="flex-1">
                <p class="font-black text-red-900 text-sm mb-1">Terjadi Kesalahan</p>
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-green-900 text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-900">🛒 Keranjang <span style="color:#72bf77">Belanja</span></h1>
        @if($carts->isNotEmpty())
            <span class="px-3 py-1 rounded-xl text-xs font-bold" style="background:rgba(114,191,119,.12);color:#3fa348">{{ $carts->count() }} item</span>
        @endif
    </div>

    @forelse($carts as $cart)
    @php
        $isDiscount = ($cart->pricing['discount_percent'] ?? 0) > 0;
        $unitPrice = $isDiscount ? $cart->pricing['unit_final'] : $cart->pricing['unit_original'];
        $totalItem = $isDiscount ? $cart->pricing['total_final'] : $cart->pricing['total_original'];
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 flex flex-col sm:flex-row gap-4 hover:shadow-md transition">
        <img src="{{ $cart->product->image ?? 'https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk' }}"
            alt="{{ $cart->product->name }}"
            class="w-24 h-24 rounded-xl object-cover flex-shrink-0 self-center sm:self-start"
            onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:#72bf77">🏪 {{ $cart->product->store->name ?? 'Arradea' }}</p>
            <h3 class="font-black text-gray-900 leading-tight mb-1">{{ $cart->product->name }}</h3>
            @if($cart->variant_key)
                <p class="text-xs text-gray-400 mb-1.5">Varian: {{ data_get($cart->product->getVariant($cart->variant_key), 'name', $cart->variant_key) }}</p>
            @endif
            <div class="flex items-center gap-2">
                @if($isDiscount)
                    <span class="text-xs text-gray-400 line-through">Rp {{ number_format($cart->pricing['unit_original'],0,',','.') }}</span>
                    <span class="font-black text-base" style="color:#72bf77">Rp {{ number_format($unitPrice,0,',','.') }}</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black text-white" style="background:#72bf77">-{{ $cart->pricing['discount_percent'] }}%</span>
                @else
                    <span class="font-black text-base text-gray-900">Rp {{ number_format($unitPrice,0,',','.') }}</span>
                @endif
            </div>
        </div>
        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3 flex-shrink-0">
            <div class="flex items-center gap-1.5 bg-gray-100 rounded-xl px-1">
                <form action="{{ route('buyer.cart.update', $cart) }}" method="POST" id="cart-qty-{{ $cart->id }}">
                    @csrf @method('PUT')
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="changeQty({{ $cart->id }}, -1)" class="w-7 h-7 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition text-sm font-black">−</button>
                        <input type="number" name="quantity" id="qty-{{ $cart->id }}" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stock }}"
                            class="w-10 bg-transparent border-none text-center font-black text-sm text-gray-900 focus:outline-none">
                        <button type="button" onclick="changeQty({{ $cart->id }}, 1)" class="w-7 h-7 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition text-sm font-black">+</button>
                    </div>
                </form>
            </div>
            <div class="text-right">
                @if($isDiscount)
                    <p class="text-[10px] text-gray-400 line-through">Rp {{ number_format($cart->pricing['total_original'],0,',','.') }}</p>
                @endif
                <p class="font-black text-gray-900">Rp {{ number_format($totalItem,0,',','.') }}</p>
            </div>
            <form action="{{ route('buyer.cart.destroy', $cart) }}" method="POST" onsubmit="return confirmSubmit(event, @js('Hapus '.$cart->product->name.' dari keranjang?'))">
                @csrf @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 flex flex-col items-center justify-center py-20 text-center">
        <span class="text-5xl mb-4">🛒</span>
        <h3 class="text-xl font-black text-gray-900 mb-2">Keranjang masih kosong</h3>
        <p class="text-sm text-gray-400 mb-6">Belum ada produk yang kamu tambahkan.</p>
        <a href="{{ route('buyer.products') }}" class="px-6 py-3 rounded-2xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">Mulai Belanja</a>
    </div>
    @endforelse

    @if($carts->isNotEmpty())
    {{-- Desktop Summary --}}
    <div class="hidden sm:block bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-0.5">
                @if(($totalOriginal ?? 0) > ($totalFinal ?? 0))
                    <p class="text-xs text-gray-400 line-through">Rp {{ number_format($totalOriginal,0,',','.') }}</p>
                    <p class="text-xs font-bold" style="color:#72bf77">Hemat Rp {{ number_format($totalOriginal - $totalFinal,0,',','.') }}</p>
                @endif
                <p class="text-sm text-gray-500 font-medium">Total Pembayaran</p>
                <p class="text-3xl font-black text-gray-900">Rp {{ number_format($totalFinal ?? 0,0,',','.') }}</p>
            </div>
            <div class="w-full sm:w-auto space-y-3">
                <form action="{{ route('buyer.cart.checkout') }}" method="POST" id="checkout-form-desktop">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment-method-desktop" value="cod">
                    <input type="hidden" name="payment_proof_base64" id="payment-proof-base64-desktop">
                    
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button type="button" onclick="selectPaymentMethod('desktop', 'cod')" id="btn-cod-desktop" class="rounded-xl border-2 border-primary-500 bg-primary-50 p-3 text-left transition">
                            <span class="block text-xs font-black text-gray-900">COD</span>
                            <span class="block text-[10px] text-gray-500 mt-1">Bayar di tempat</span>
                        </button>
                        <a href="{{ $hasQrisSeller ? route('buyer.cart.qris') : '#' }}" id="btn-qris-desktop" class="rounded-xl border-2 p-3 text-left transition {{ $hasQrisSeller ? 'border-gray-200 bg-white hover:border-primary-300' : 'border-gray-200 bg-gray-100 cursor-not-allowed' }}" {{ $hasQrisSeller ? '' : 'onclick="return false;"' }}>
                            <span class="block text-xs font-black {{ $hasQrisSeller ? 'text-gray-900' : 'text-gray-400' }}">QRIS Manual</span>
                            <span class="block text-[10px] {{ $hasQrisSeller ? 'text-gray-500' : 'text-gray-400' }} mt-1">
                                @if($hasQrisSeller)
                                    Scan & bayar
                                @else
                                    Seller belum setup
                                @endif
                            </span>
                        </a>
                    </div>
                    
                    @if(!$hasQrisSeller && $singleStore)
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs font-bold text-amber-900">⚠️ QRIS Belum Tersedia</p>
                        <p class="text-[10px] text-amber-700 mt-1">
                            Seller <strong>{{ $singleStore->name }}</strong> belum mengaktifkan pembayaran QRIS. 
                            @if($singleSeller)
                                @if(!$singleSeller->qris_image)
                                    Seller perlu upload gambar QRIS
                                @endif
                                @if(!$singleSeller->payment_name)
                                    {{ !$singleSeller->qris_image ? ' dan' : 'Seller perlu' }} mengisi nama penerima
                                @endif
                                di halaman pengaturan toko.
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full sm:w-72 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 transition mb-2 resize-none"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)">{{ old('notes') }}</textarea>
                    <button type="submit" class="w-full sm:w-72 py-3.5 rounded-xl font-black text-base text-white transition hover:opacity-90 hover:-translate-y-0.5" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                        Pesan Sekarang →
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Mobile Sticky Checkout --}}
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-2xl" style="padding-bottom:env(safe-area-inset-bottom)">
        <div class="px-5 py-3">
            <div class="flex items-center justify-between mb-3">
                <div>
                    @if(($totalOriginal ?? 0) > ($totalFinal ?? 0))
                        <p class="text-[10px] text-gray-400 line-through">Rp {{ number_format($totalOriginal,0,',','.') }}</p>
                    @endif
                    <p class="text-xs text-gray-500 font-medium">Total</p>
                    <p class="text-xl font-black text-gray-900">Rp {{ number_format($totalFinal ?? 0,0,',','.') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('mobile-checkout-form').classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-gray-100">
                    + Catatan
                </button>
            </div>
            <form id="mobile-checkout-form-main" action="{{ route('buyer.cart.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-mobile" value="cod">
                <input type="hidden" name="payment_proof_base64" id="payment-proof-base64-mobile">
                
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <button type="button" onclick="selectPaymentMethod('mobile', 'cod')" id="btn-cod-mobile" class="rounded-xl border border-primary-500 bg-primary-50 p-2.5 text-left transition">
                        <span class="block text-[10px] font-black text-gray-900">COD</span>
                        <span class="block text-[9px] text-gray-500 mt-0.5">Bayar di tempat</span>
                    </button>
                    <a href="{{ $hasQrisSeller ? route('buyer.cart.qris') : '#' }}" id="btn-qris-mobile" class="rounded-xl border p-2.5 text-left transition {{ $hasQrisSeller ? 'border-gray-200 bg-gray-50 hover:border-primary-300' : 'border-gray-200 bg-gray-100 cursor-not-allowed' }}" {{ $hasQrisSeller ? '' : 'onclick="return false;"' }}>
                        <span class="block text-[10px] font-black {{ $hasQrisSeller ? 'text-gray-900' : 'text-gray-400' }}">QRIS</span>
                        <span class="block text-[9px] {{ $hasQrisSeller ? 'text-gray-500' : 'text-gray-400' }} mt-0.5">
                            @if($hasQrisSeller)
                                Scan & bayar
                            @else
                                Belum tersedia
                            @endif
                        </span>
                    </a>
                </div>
                
                @if(!$hasQrisSeller && $singleStore)
                <div class="mb-3 p-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-[10px] font-bold text-amber-900">⚠️ QRIS belum tersedia</p>
                    <p class="text-[9px] text-amber-700 mt-0.5">
                        @if($singleSeller)
                            @if(!$singleSeller->qris_image && !$singleSeller->payment_name)
                                Seller belum upload QRIS & nama penerima.
                            @elseif(!$singleSeller->qris_image)
                                Seller belum upload gambar QRIS.
                            @elseif(!$singleSeller->payment_name)
                                Seller belum isi nama penerima.
                            @endif
                        @else
                            Seller belum setup QRIS.
                        @endif
                    </p>
                </div>
                @endif
                
                <div id="mobile-checkout-form" class="hidden mb-3">
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 transition resize-none"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl font-black text-base text-white transition active:scale-95" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                    Pesan Sekarang →
                </button>
            </form>
        </div>
    </div>
    {{-- Spacer for sticky button --}}
    <div class="sm:hidden h-32"></div>
    @endif
</div>

@push('scripts')
<script>
function selectPaymentMethod(scope, method) {
    const methodInput = document.getElementById('payment-method-' + scope);
    const codBtn = document.getElementById('btn-cod-' + scope);
    
    if (methodInput) {
        methodInput.value = method;
    }
}

function changeQty(cartId, delta){
    const input = document.getElementById('qty-'+cartId);
    if(!input) return;
    const min = parseInt(input.min)||1;
    const max = parseInt(input.max)||99;
    const newVal = Math.min(max, Math.max(min, parseInt(input.value)+delta));
    input.value = newVal;
    document.getElementById('cart-qty-'+cartId).submit();
}
</script>
@endpush
@endsection