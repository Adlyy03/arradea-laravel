@extends('layouts.dashboard')

@section('title', 'Keranjang Belanja - Arradea')
@section('page_title', 'Keranjang Belanja')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl text-green-800 bg-green-50 border border-green-200">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl text-red-800 bg-red-50 border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse($carts as $cart)
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-6 border-b border-gray-50 last:border-b-0">
                <img src="{{ $cart->product->image ?? 'https://via.placeholder.com/100?text=No+Image' }}" alt="{{ $cart->product->name }}" class="w-24 h-24 rounded-2xl object-cover shadow-sm">
 
                <div class="flex-1">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $cart->product->name }}</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $cart->product->store->name }}</p>
                    <p class="text-xs font-medium text-gray-500 mt-1">Varian: {{ data_get($cart->product->getVariant($cart->variant_key), 'name', 'Default') }}</p>
                    @if(($cart->pricing['discount_percent'] ?? 0) > 0)
                        <p class="text-xs font-bold text-red-500 mt-1 line-through">Rp {{ number_format($cart->pricing['unit_original'], 0, ',', '.') }}</p>
                        <p class="text-lg font-bold text-primary-600">Rp {{ number_format($cart->pricing['unit_final'], 0, ',', '.') }} <span class="text-xs text-green-600">(-{{ rtrim(rtrim(number_format($cart->pricing['discount_percent'], 2, '.', ''), '0'), '.') }}%)</span></p>
                    @else
                        <p class="text-lg font-bold text-primary-600 mt-1">Rp {{ number_format($cart->pricing['unit_original'], 0, ',', '.') }}</p>
                    @endif
                </div>

                <div class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-6 sm:gap-5 lg:gap-10 pt-4 sm:pt-0 border-t sm:border-t-0 border-gray-50">
                    <form action="{{ route('buyer.cart.update', $cart) }}" method="POST" class="flex items-center bg-gray-50 rounded-xl px-2">
                        @csrf @method('PUT')
                        <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stock }}" class="w-14 bg-transparent border-none focus:ring-0 text-center font-bold text-gray-900">
                        <button type="submit" class="p-2 text-primary-600 hover:text-primary-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>

                    <div class="text-right">
                        @if(($cart->pricing['discount_percent'] ?? 0) > 0)
                            <p class="text-xs font-bold text-gray-400 line-through">Rp {{ number_format($cart->pricing['total_original'], 0, ',', '.') }}</p>
                        @endif
                        <p class="text-xl font-black text-gray-900">Rp {{ number_format($cart->pricing['total_final'], 0, ',', '.') }}</p>
                    </div>

                    <form action="{{ route('buyer.cart.destroy', $cart) }}" method="POST" onsubmit="return confirmSubmit(event, @js('Hapus item ini?'))">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 lg:py-24">
                <h3 class="text-3xl font-black mb-4">Keranjang Kosong</h3>
                <p class="text-gray-500 mb-8">Belum ada produk di keranjang Anda.</p>
                <a href="{{ route('buyer.products') }}" class="px-8 py-4 bg-primary-600 text-white rounded-2xl font-black">Belanja Sekarang</a>
            </div>
        @endforelse

        @if($carts->isNotEmpty())
            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-6">
                <div class="text-center sm:text-left space-y-1">
                    @if(($totalOriginal ?? 0) > ($totalFinal ?? 0))
                        <p class="text-sm font-bold text-gray-400 line-through">Harga Asli: Rp {{ number_format($totalOriginal, 0, ',', '.') }}</p>
                    @endif
                    <div class="text-3xl font-black text-gray-900 italic tracking-tighter">Total Bayar: <br class="sm:hidden"> <span class="text-primary-600">Rp {{ number_format($totalFinal ?? 0, 0, ',', '.') }}</span></div>
                </div>
                <form action="{{ route('buyer.cart.checkout') }}" method="POST" class="w-full sm:w-auto space-y-3">
                    @csrf
                    <textarea name="notes" rows="3" maxlength="1000" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 focus:border-primary-600 focus:outline-none" placeholder="Catatan untuk penjual (opsional), contoh: kirim sore hari.">{{ old('notes') }}</textarea>
                    <button type="submit" class="w-full px-6 lg:px-12 py-5 bg-primary-600 text-white rounded-2xl font-black text-xl hover:bg-primary-700 shadow-2xl shadow-primary-200 transition active:scale-95">Bayar Sekarang →</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection