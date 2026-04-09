@extends('layouts.dashboard')

@section('title', 'Detail Produk - Arradea')
@section('page_title', 'Detail & Pesan Produk')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[3rem] p-6 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl text-green-800 bg-green-50 border border-green-200 text-sm font-bold">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl text-red-800 bg-red-50 border border-red-200 text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-6 lg:gap-12">
            <div class="aspect-square lg:aspect-auto rounded-2xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[3rem] overflow-hidden shadow-inner">
                <img src="{{ $product->image ?? 'https://via.placeholder.com/700?text=No+Image' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>

            <div class="space-y-8">
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $product->store->name ?? 'Arradea' }}</p>
                        <h2 class="text-3xl lg:text-5xl font-black text-gray-900 tracking-tight leading-tight">{{ $product->name }}</h2>
                        <p class="text-2xl lg:text-3xl font-black text-primary-700 mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="text-xs font-bold text-gray-500 mt-1">Sisa Stok: <span class="text-gray-900">{{ $product->stock }}</span></p>
                    </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Deskripsi</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>

                @auth
                    @if(auth()->user()->role === 'buyer')
                        <form action="{{ route('buyer.cart.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="space-y-2">
                                <label class="block text-sm font-black text-gray-500">Jumlah</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-28 border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                <button type="submit" class="flex-1 px-8 py-4 bg-primary-600 text-white rounded-2xl font-black text-lg hover:bg-primary-700 shadow-xl shadow-primary-200 transition active:scale-95">Tambah ke Keranjang</button>
                                <a href="{{ route('buyer.cart') }}" class="px-8 py-4 bg-gray-50 text-gray-700 border border-gray-100 rounded-2xl font-black text-lg hover:bg-gray-100 transition text-center italic">Checkout →</a>
                            </div>
                        </form>
                    @else
                        <div class="p-6 bg-amber-50 border border-amber-200 rounded-2xl">
                            <p class="text-amber-800 font-bold">Hanya pembeli yang dapat menambahkan produk ke keranjang.</p>
                        </div>
                    @endif
                @else
                    <div class="space-y-4">
                        <div class="p-6 bg-blue-50 border border-blue-200 rounded-2xl">
                            <p class="text-blue-800 font-bold mb-4">Login terlebih dahulu untuk menambahkan produk ke keranjang.</p>
                            <div class="flex gap-4">
                                <a href="{{ route('login') }}" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-black hover:bg-primary-700 transition">Login</a>
                                <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-50 text-gray-700 border border-gray-200 rounded-xl font-black hover:bg-gray-100 transition">Daftar</a>
                            </div>
                        </div>
                    </div>
                @endauth

                <a href="{{ route('buyer.products') }}" class="inline-block mt-4 text-sm font-bold text-gray-500 hover:text-primary-600">← Kembali ke semua produk</a>
            </div>
        </div>
    </div>
</div>
@endsection