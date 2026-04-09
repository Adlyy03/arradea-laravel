@extends('layouts.dashboard')

@section('title', 'Temukan Produk - Arradea')
@section('page_title', 'Semua Produk')

@section('content')
<div class="space-y-6 lg:space-y-12">
        <!-- Categories Filter -->
        <div class="flex overflow-x-auto gap-3 pb-6 mb-2 scrollbar-none">
            <a href="{{ route('buyer.products') }}" class="whitespace-nowrap px-6 py-3 rounded-xl font-bold text-sm transition {{ !request('category') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-50 text-gray-600 hover:bg-primary-50 hover:text-primary-600' }}">Semua Kategori</a>
            @foreach($categories as $category)
                <a href="{{ route('buyer.products', ['category' => $category->slug]) }}" class="whitespace-nowrap px-6 py-3 rounded-xl font-bold text-sm transition {{ request('category') == $category->slug ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-50 text-gray-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

    <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            @forelse($products as $product)
                <div class="group bg-white rounded-2xl lg:rounded-[3rem] p-4 lg:p-6 border border-gray-100 shadow-sm hover:shadow-2xl transition">
                    <div class="aspect-square overflow-hidden rounded-xl lg:rounded-[2.2rem] mb-4">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/400?text=No+Image' }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                    </div>
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-[9px] lg:text-[10px] font-black text-primary-500 uppercase tracking-widest">{{ $product->category->name ?? 'Umum' }}</p>
                    </div>
                    <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $product->store->name ?? 'Arradea' }}</p>
                    <h3 class="text-base lg:text-xl font-black text-gray-900 line-clamp-2 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-500 text-xs lg:text-sm line-clamp-2 mb-4">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-lg lg:text-2xl font-black text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="text-[10px] lg:text-xs font-black uppercase tracking-widest {{ $product->stock > 5 ? 'text-green-600' : 'text-red-500' }}">{{ $product->stock }} stok</span>
                    </div>
                    <a href="/products/{{ $product->id }}" class="block text-center px-4 py-3 bg-primary-600 text-white rounded-xl font-black hover:bg-primary-700 transition lg:text-base text-sm">Lihat & Beli</a>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-12 lg:py-24 text-gray-400">
                    <h3 class="text-3xl font-black mb-4">Belum ada produk</h3>
                    <p>Tunggu seller menambahkan produk baru atau buat dulu toko Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection