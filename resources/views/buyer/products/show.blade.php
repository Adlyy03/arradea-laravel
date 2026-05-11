@extends('layouts.dashboard')

@section('title', 'Detail Produk - Arradea')
@section('page_title', 'Detail & Pesan Produk')

@push('styles')
<style>
    /* Mobile product detail optimizations - Ultra Compact */
    @media(max-width:1023px){
        .product-detail-back { padding: 6px 12px !important; font-size: 12px !important; border-radius: 10px !important; }
        .product-detail-back svg { width: 16px !important; height: 16px !important; }
        .product-detail-breadcrumb { font-size: 11px !important; }
        .product-detail-card { padding: 12px !important; border-radius: 12px !important; }
        .product-detail-image { 
            aspect-ratio: 1 / 1 !important;
            max-height: 280px !important;
            border-radius: 12px !important;
        }
        .product-detail-discount-badge { 
            top: 10px !important; 
            right: 10px !important; 
            padding: 4px 8px !important; 
            font-size: 10px !important; 
        }
        .product-detail-store { font-size: 10px !important; }
        .product-detail-store svg { width: 14px !important; height: 14px !important; }
        .product-detail-title { font-size: 18px !important; line-height: 1.3 !important; }
        .product-detail-price-box { padding: 12px !important; border-radius: 12px !important; }
        .product-detail-price { font-size: 22px !important; }
        .product-detail-price-old { font-size: 14px !important; }
        .product-detail-stock { font-size: 11px !important; padding-top: 8px !important; margin-top: 8px !important; }
        .product-detail-stock svg { width: 14px !important; height: 14px !important; }
        .product-detail-desc { padding: 12px !important; border-radius: 10px !important; }
        .product-detail-desc h3 { font-size: 11px !important; margin-bottom: 8px !important; }
        .product-detail-desc svg { width: 14px !important; height: 14px !important; }
        .product-detail-desc p { font-size: 12px !important; line-height: 1.5 !important; }
    }
    
    @media(max-width:375px){
        .product-detail-image { max-height: 240px !important; }
        .product-detail-title { font-size: 16px !important; }
        .product-detail-price { font-size: 20px !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-4 lg:space-y-6">
    <!-- Back Button -->
    <div class="flex items-center gap-2 lg:gap-3">
        <a href="{{ route('buyer.products') }}" class="product-detail-back inline-flex items-center gap-1.5 lg:gap-2 px-3 lg:px-4 py-2 lg:py-2.5 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg lg:rounded-xl font-bold text-xs lg:text-sm text-gray-700 transition-all hover:shadow-md active:scale-95 group">
            <svg class="w-4 lg:w-5 h-4 lg:h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Kembali</span>
        </a>
        <div class="h-5 lg:h-6 w-px bg-gray-200"></div>
        <nav class="product-detail-breadcrumb text-xs lg:text-sm text-gray-500">
            <a href="{{ route('buyer.products') }}" class="hover:text-primary-600 transition">Produk</a>
            <span class="mx-1 lg:mx-2">/</span>
            <span class="text-gray-900 font-semibold">{{ Str::limit($product->name, 20) }}</span>
        </nav>
    </div>

    <!-- Product Detail Card -->
    <div class="product-detail-card bg-white rounded-xl lg:rounded-2xl lg:rounded-3xl p-4 lg:p-6 lg:p-8 shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="mb-4 lg:mb-6 p-3 lg:p-4 rounded-lg lg:rounded-xl text-green-800 bg-green-50 border border-green-200 text-xs lg:text-sm font-bold flex items-center gap-2 lg:gap-3">
                <svg class="w-4 lg:w-5 h-4 lg:h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 lg:mb-6 p-3 lg:p-4 rounded-lg lg:rounded-xl text-red-800 bg-red-50 border border-red-200 text-xs lg:text-sm">
                <ul class="list-disc pl-4 lg:pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 lg:gap-10">
            <!-- Product Image -->
            <div class="relative">
                <div class="product-detail-image aspect-square rounded-xl lg:rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 shadow-lg">
                    <img src="{{ $product->image ?? 'https://via.placeholder.com/700?text=No+Image' }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center block">
                </div>
                @php $basePricing = $product->calculatePricing('default', 1); @endphp
                @if(($basePricing['discount_percent'] ?? 0) > 0)
                    <div class="product-detail-discount-badge absolute top-3 lg:top-4 right-3 lg:right-4 bg-red-500 text-white px-2 lg:px-3 py-1 lg:py-1.5 rounded-full text-[10px] lg:text-xs font-black shadow-lg">
                        -{{ $basePricing['discount_percent'] }}%
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-4 lg:space-y-6">
                <!-- Store & Title -->
                <div class="space-y-1.5 lg:space-y-2">
                    <div class="product-detail-store flex items-center gap-1.5 lg:gap-2">
                        <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-[10px] lg:text-xs font-bold uppercase tracking-wider text-gray-500">{{ $product->store->name ?? 'Arradea' }}</span>
                        @if($product->store)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] lg:text-[10px] font-black {{ $product->store->store_status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1 lg:w-1.5 h-1 lg:h-1.5 rounded-full {{ $product->store->store_status === 'open' ? 'bg-green-500' : 'bg-gray-400' }} animate-pulse"></span>
                                {{ $product->store->store_status === 'open' ? 'Buka' : 'Tutup' }}
                            </span>
                        @endif
                    </div>
                    <h1 class="product-detail-title text-2xl lg:text-3xl lg:text-4xl font-black text-gray-900 leading-tight">{{ $product->name }}</h1>
                </div>

                <!-- Price -->
                <div class="product-detail-price-box bg-gradient-to-br from-primary-50 to-green-50 border border-primary-100 rounded-xl lg:rounded-2xl p-4 lg:p-5">
                    @php $basePricing = $product->calculatePricing('default', 1); @endphp
                    @if(($basePricing['discount_percent'] ?? 0) > 0)
                        <div class="flex items-baseline gap-2 lg:gap-3">
                            <span class="product-detail-price text-2xl lg:text-3xl lg:text-4xl font-black text-primary-700">Rp {{ number_format($basePricing['unit_final'], 0, ',', '.') }}</span>
                            <span class="product-detail-price-old text-sm lg:text-lg font-bold text-gray-400 line-through">Rp {{ number_format($basePricing['unit_original'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] lg:text-xs font-bold text-primary-600 mt-1">Hemat Rp {{ number_format($basePricing['unit_original'] - $basePricing['unit_final'], 0, ',', '.') }}</p>
                    @else
                        <span class="product-detail-price text-2xl lg:text-3xl lg:text-4xl font-black text-primary-700">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    @endif
                    <div class="product-detail-stock flex items-center gap-1.5 lg:gap-2 mt-2 lg:mt-3 pt-2 lg:pt-3 border-t border-primary-100">
                        <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="text-xs lg:text-sm font-bold text-gray-600">Stok: <span class="text-gray-900">{{ $product->stock }} unit</span></span>
                    </div>
                </div>

                <!-- Description -->
                <div class="product-detail-desc bg-gray-50 rounded-lg lg:rounded-xl p-4 lg:p-5 border border-gray-100">
                    <h3 class="text-xs lg:text-sm font-black uppercase tracking-wider text-gray-500 mb-2 lg:mb-3 flex items-center gap-1.5 lg:gap-2">
                        <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Deskripsi
                    </h3>
                    <p class="text-gray-700 leading-relaxed text-xs lg:text-sm">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>

                <!-- Purchase Form / Actions -->
                @auth
                    @if($product->store && (int) auth()->id() === (int) $product->store->user_id)
                        <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-6 flex items-start gap-4">
                            <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="text-amber-900 font-bold text-sm">Ini produk dari toko Anda</p>
                                <p class="text-amber-700 text-xs mt-1">Anda tidak bisa membeli produk milik toko sendiri.</p>
                            </div>
                        </div>
                    @elseif($product->store && $product->store->store_status !== 'open')
                        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6 flex items-start gap-4">
                            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-red-900 font-bold text-sm">Toko Sedang Tutup</p>
                                <p class="text-red-700 text-xs mt-1">Toko ini sedang tidak beroperasi. Silakan coba lagi nanti saat toko sudah buka.</p>
                                @if($product->store->open_time && $product->store->close_time)
                                    <p class="text-red-600 text-xs mt-2 font-semibold">Jam operasional: {{ substr($product->store->open_time, 0, 5) }} - {{ substr($product->store->close_time, 0, 5) }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <form action="{{ route('buyer.cart.store') }}" method="POST" class="space-y-5 bg-white border-2 border-gray-200 rounded-2xl p-6">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            @if(!empty($product->variants))
                                <div class="space-y-2">
                                    <label class="block text-sm font-black uppercase tracking-wider text-gray-500">Pilih Varian</label>
                                    <select name="variant_key" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-semibold text-gray-700 transition">
                                        <option value="default">Default Produk</option>
                                        @foreach($product->variants as $variant)
                                            @php
                                                $variantPricing = $product->calculatePricing($variant['key'] ?? null, 1);
                                            @endphp
                                            <option value="{{ $variant['key'] ?? '' }}">
                                                {{ $variant['name'] ?? 'Varian' }} - Rp {{ number_format($variantPricing['unit_final'], 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="space-y-2">
                                <label class="block text-sm font-black uppercase tracking-wider text-gray-500">Jumlah</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-32 border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-bold text-gray-900 transition" required>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-primary-600 to-green-600 text-white rounded-xl font-black text-base hover:from-primary-700 hover:to-green-700 shadow-lg shadow-primary-200 transition-all active:scale-95 flex items-center justify-center gap-2 {{ $product->stock === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock === 0 ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $product->stock === 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                                </button>
                                <a href="{{ route('buyer.cart') }}" class="px-6 py-4 bg-white text-gray-700 border-2 border-gray-200 rounded-xl font-black text-base hover:bg-gray-50 hover:border-gray-300 transition-all text-center flex items-center justify-center gap-2">
                                    Lihat Keranjang
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-blue-900 font-bold text-sm">Login diperlukan</p>
                                <p class="text-blue-700 text-xs mt-1">Silakan login atau daftar untuk menambahkan produk ke keranjang.</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('login') }}" class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-xl font-black text-sm hover:bg-primary-700 transition-all text-center active:scale-95">Login</a>
                            <a href="{{ route('register') }}" class="flex-1 px-6 py-3 bg-white text-gray-700 border-2 border-gray-200 rounded-xl font-black text-sm hover:bg-gray-50 transition-all text-center active:scale-95">Daftar</a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection