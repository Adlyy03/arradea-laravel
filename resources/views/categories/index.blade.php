@extends('layouts.app')

@section('title', 'Kategori Produk - Arradea')
@section('page_title', 'Semua Kategori')

@section('content')
<div class="max-w-7xl mx-auto px-6 sm:px-8 py-8 lg:py-16 lg:py-16 lg:py-32">
    <!-- Header -->
    <div class="text-center mb-12 lg:mb-20">
        <h1 class="text-4xl lg:text-6xl font-black tracking-tighter mb-4">Kategori <span class="text-primary-600">Produk</span>.</h1>
        <p class="text-gray-400 text-lg lg:text-xl font-medium max-w-2xl mx-auto">Jelajahi berbagai kategori produk dari penjual terpercaya di sekitar Anda.</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-6 lg:gap-12">
        @forelse ($categories as $category)
            <div class="group bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-6 lg:p-12 shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden">
                <div class="relative">
                    <div class="w-20 h-20 lg:w-24 lg:h-24 bg-primary-50 rounded-2xl lg:rounded-3xl flex items-center justify-center mb-6 lg:mb-8 group-hover:bg-primary-100 transition-colors">
                        <span class="text-3xl lg:text-4xl font-black text-primary-600">{{ substr($category->name, 0, 1) }}</span>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-black text-gray-900 mb-4">{{ $category->name }}</h3>
                    <p class="text-gray-500 font-medium mb-6 lg:mb-8 leading-relaxed">{{ $category->description }}</p>

                    @if($category->children->count() > 0)
                        <div class="mb-6 lg:mb-8">
                            <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-3">Sub Kategori:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($category->children->take(3) as $child)
                                    <a href="{{ route('categories.show', $child) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-primary-100 hover:text-primary-700 transition-colors">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                                @if($category->children->count() > 3)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm font-medium">
                                        +{{ $category->children->count() - 3 }} lagi
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400 font-medium">
                            {{ $category->getProductsCount() }} produk tersedia
                        </div>
                        <a href="{{ route('categories.show', $category) }}" class="px-6 py-3 bg-primary-600 text-white rounded-2xl font-black text-sm hover:bg-primary-700 transition-colors">
                            Jelajahi →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 lg:py-20">
                <div class="text-4xl lg:text-6xl mb-4">📂</div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Kategori</h3>
                <p class="text-gray-500 font-medium">Kategori produk akan segera ditambahkan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection