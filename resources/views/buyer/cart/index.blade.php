@extends('layouts.dashboard')
@section('title', 'Keranjang Belanja — Arradea')
@section('page_title', 'Keranjang')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 fade-up">

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
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
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
                <form action="{{ route('buyer.cart.checkout') }}" method="POST">
                    @csrf
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
    @endif
</div>

@push('scripts')
<script>
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