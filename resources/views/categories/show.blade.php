@extends('layouts.app')

@section('title', $category->name . ' - Arradea')
@section('page_title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-6 sm:px-8 py-8 lg:py-16 lg:py-16 lg:py-32">
    <!-- Header -->
    <div class="mb-12 lg:mb-20">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('categories.index') }}" class="text-primary-600 hover:text-primary-700 font-medium">← Kembali ke Kategori</a>
        </div>
        <h1 class="text-4xl lg:text-6xl font-black tracking-tighter mb-4">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-400 text-lg lg:text-xl font-medium max-w-3xl">{{ $category->description }}</p>
        @endif
        <div class="flex items-center gap-6 mt-6">
            <div class="text-sm text-gray-500 font-medium">
                {{ $products->total() }} produk ditemukan
            </div>
            @if($category->parent)
                <div class="text-sm text-gray-400">
                    Sub kategori dari: <a href="{{ route('categories.show', $category->parent) }}" class="text-primary-600 hover:underline">{{ $category->parent->name }}</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Subcategories (if this is a parent category) -->
    @if($category->children->count() > 0)
        <div class="mb-12 lg:mb-16">
            <h2 class="text-2xl font-black text-gray-900 mb-6">Sub Kategori</h2>
            <div class="flex flex-wrap gap-4">
                @foreach($category->children as $child)
                    <a href="{{ route('categories.show', $child) }}"
                       class="px-6 py-3 bg-white border border-gray-200 rounded-2xl font-medium text-gray-700 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700 transition-colors">
                        {{ $child->name }} ({{ $child->getProductsCount() }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 lg:gap-12 mb-12 lg:mb-20">
            @foreach ($products as $product)
                <div class="group bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-8 shadow-sm hover:shadow-2xl transition duration-500 border border-gray-100 flex flex-col h-full overflow-hidden">
                    <div class="relative aspect-square overflow-hidden rounded-2xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[3rem] mb-6 lg:mb-10 shrink-0 shadow-inner">
                        <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-125 transition duration-1000">
                    </div>
                    <div class="flex-1 space-y-4 lg:space-y-6">
                        <div class="space-y-1">
                            <h3 class="text-xl lg:text-2xl font-black text-gray-900 leading-tight tracking-tight line-clamp-1">{{ $product->name }}</h3>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">🏪 {{ $product->store->name ?? 'Arradea' }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-2xl lg:text-3xl font-black text-gray-900 italic tracking-tighter">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        <a href="/products/{{ $product->id }}" class="w-full h-14 lg:h-18 bg-primary-600 text-white rounded-xl lg:rounded-[1.8rem] font-black text-base lg:text-lg hover:bg-primary-700 shadow-xl lg:shadow-2xl shadow-primary-200 transition-all lg:opacity-0 lg:group-hover:opacity-100 lg:translate-y-10 lg:group-hover:translate-y-0 flex items-center justify-center">
                            + Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-10 lg:py-20">
            <div class="text-4xl lg:text-6xl mb-4">📦</div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Produk</h3>
            <p class="text-gray-500 font-medium mb-6">Produk dalam kategori ini akan segera ditambahkan oleh penjual.</p>
            <a href="{{ route('categories.index') }}" class="px-8 py-4 bg-primary-600 text-white rounded-2xl font-black hover:bg-primary-700 transition-colors">
                Jelajahi Kategori Lain
            </a>
        </div>
    @endif
</div>
@endsection