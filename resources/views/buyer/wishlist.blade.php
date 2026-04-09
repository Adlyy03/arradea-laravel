@extends('layouts.dashboard')

@section('title', 'Wishlist - Arradea')
@section('page_title', 'Produk Favorit Saya')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Produk <span class="text-primary-600 underline underline-offset-4 lg:underline-offset-8 decoration-4 lg:decoration-8">Favorit</span>.</h1>
            <p class="text-gray-500 text-base lg:text-lg font-medium leading-relaxed">Kelola daftar produk impian Anda dan mulai checkout kapan saja!</p>
        </div>
        <div class="flex gap-4 w-full lg:w-auto">
            <a href="{{ route('buyer.products') }}" class="flex-1 lg:flex-none px-8 lg:px-5 lg:px-10 py-5 bg-black text-white rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl hover:scale-105 active:scale-95 transition-all text-center">Belanja Sekarang →</a>
        </div>
    </div>

    <!-- Wishlist Grid -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tighter">Produk <span class="text-primary-600">Favorit</span>.</h2>
        </div>

        @if(isset($wishlists) && $wishlists->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($wishlists as $wishlist)
                    @php $product = $wishlist->product; @endphp
                    <a href="{{ route('buyer.products.show', $product) }}" class="group">
                        <div class="relative rounded-2xl lg:rounded-3xl overflow-hidden bg-gray-100 shadow-md border border-gray-100 group-hover:shadow-xl transition-all h-60">
                            <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop'">
                            
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                                <div class="w-full p-4">
                                    <button class="w-full px-4 py-3 bg-accent text-white rounded-xl font-black text-sm" onclick="event.preventDefault(); document.getElementById('remove-{{ $product->id }}').submit();">Hapus dari Favorit</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <p class="font-black text-gray-900 text-sm line-clamp-2 group-hover:text-primary-600 transition">{{ $product->name }}</p>
                            <div class="flex justify-between items-center">
                                <p class="text-lg font-black text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <span class="text-[10px] font-black bg-primary-50 text-primary-600 px-3 py-1 rounded-full">{{ $product->stock }} Ready</span>
                            </div>
                        </div>

                        <!-- Hidden form for removing from wishlist -->
                        <form id="remove-{{ $product->id }}" action="{{ route('buyer.wishlist.toggle', $product) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </a>
                @endforeach
            </div>
        @else
            <div class="py-12 lg:py-24 text-center space-y-6">
                <div class="text-5xl lg:text-7xl">❤️‍🔥</div>
                <h3 class="text-2xl font-black text-gray-900">Belum Ada Produk Favorit.</h3>
                <p class="text-gray-500 font-medium">Jelajahi produk dan tambahkan ke wishlist Anda!</p>
                <a href="{{ route('buyer.products') }}" class="inline-block px-6 lg:px-12 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-3xl font-black text-lg hover:scale-105 transition-all">
                    Mulai Menjelajah
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
