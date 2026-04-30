@extends('layouts.app')
@section('title', 'Arradea — Marketplace Warga')

@section('content')
@php
    $products = \App\Models\Product::with('store')->latest()->take(12)->get();
    $featuredCategories = \App\Models\Category::featured()->parents()->orderBy('sort_order')->get();
@endphp

<style>
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
    .float { animation: float 4s ease-in-out infinite; }
    .fade-up { animation: fadeUp .6s ease both; }
    .fade-up-2 { animation: fadeUp .6s ease .1s both; }
    .fade-up-3 { animation: fadeUp .6s ease .2s both; }
    .product-card:hover .product-img { transform: scale(1.07); }
    .product-card:hover .buy-btn { opacity:1; transform:translateY(0); }
    .buy-btn { opacity:0; transform:translateY(8px); transition:all .3s; }
    .hero-blob { position:absolute; border-radius:50%; filter:blur(80px); pointer-events:none; }
</style>

{{-- HERO --}}
<section class="relative overflow-hidden bg-white min-h-[82vh] flex items-center">
    {{-- Blobs --}}
    <div class="hero-blob w-96 h-96 -top-20 -right-20 opacity-20" style="background:#72bf77"></div>
    <div class="hero-blob w-64 h-64 bottom-10 -left-10 opacity-10" style="background:#4db85a"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left --}}
            <div class="space-y-8 fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold" style="background:rgba(114,191,119,.12);color:#3fa348;border:1px solid rgba(114,191,119,.25)">
                    🏘️ Pasar Warga Arradea
                </div>

                <h1 class="text-5xl lg:text-7xl font-black tracking-tighter leading-[0.85] text-gray-900">
                    Segar<br>
                    <span style="color:#72bf77">Dekat</span><br>
                    Lengkap.
                </h1>

                <p class="text-lg text-gray-500 leading-relaxed max-w-md font-medium">
                    Dukung jualan tetangga! Dari makanan ibu-ibu komplek sampai jasa profesional, semua ada di sini.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="#feed" class="px-7 py-3.5 rounded-2xl font-bold text-white text-base transition hover:opacity-90 hover:-translate-y-0.5" style="background:#72bf77;box-shadow:0 8px 30px rgba(114,191,119,.4)">
                        Belanja Sekarang
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-2xl font-bold text-gray-700 text-base bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
                            Gabung Seller →
                        </a>
                    @else
                        <a href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('buyer.dashboard') }}" class="px-7 py-3.5 rounded-2xl font-bold text-gray-700 text-base bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
                            Dashboard Saya →
                        </a>
                    @endguest
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-2xl font-black text-gray-900">500+</p>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Transaksi</p>
                    </div>
                    <div class="w-px bg-gray-100"></div>
                    <div>
                        <p class="text-2xl font-black text-gray-900">100+</p>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Seller Aktif</p>
                    </div>
                    <div class="w-px bg-gray-100"></div>
                    <div>
                        <p class="text-2xl font-black" style="color:#72bf77">★ 4.9</p>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Rating</p>
                    </div>
                </div>
            </div>

            {{-- Right: Floating Cards --}}
            <div class="relative hidden lg:block h-[480px] fade-up-2">
                {{-- Main visual --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-72 h-72 rounded-[4rem] flex items-center justify-center text-8xl shadow-2xl shadow-green-200/50" style="background:linear-gradient(135deg,#f0faf1,#d8f3da)">
                        🏪
                    </div>
                </div>

                {{-- Floating card 1 --}}
                <div class="float absolute top-8 right-4 bg-white rounded-2xl p-4 shadow-xl border border-gray-100" style="animation-delay:0s">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-xl">🛒</div>
                        <div>
                            <p class="text-xs font-black text-gray-900">Pesanan Baru!</p>
                            <p class="text-[10px] text-gray-400">2 menit yang lalu</p>
                        </div>
                    </div>
                </div>

                {{-- Floating card 2 --}}
                <div class="float absolute bottom-12 left-4 bg-white rounded-2xl p-4 shadow-xl border border-gray-100" style="animation-delay:1.5s">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-xl">⭐</div>
                        <div>
                            <p class="text-xs font-black text-gray-900">Rating Sempurna</p>
                            <p class="text-[10px] text-gray-400">Baru direview</p>
                        </div>
                    </div>
                </div>

                {{-- Floating card 3 --}}
                <div class="float absolute top-1/2 right-0 bg-white rounded-2xl p-3 shadow-xl border border-gray-100" style="animation-delay:.8s">
                    <p class="text-xs font-black text-gray-700">💰 Rp 350.000</p>
                    <p class="text-[10px] text-gray-400">Penjualan hari ini</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
@if($featuredCategories->count() > 0)
<section class="py-16 bg-[#f7faf7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black tracking-tight text-gray-900">Kategori <span style="color:#72bf77">Populer</span></h2>
            <p class="text-gray-400 mt-2 font-medium">Temukan produk berdasarkan kategori favoritmu</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($featuredCategories as $cat)
            <a href="{{ route('categories.show', $cat->slug) }}" class="group bg-white rounded-2xl p-5 text-center border border-gray-100 hover:border-green-200 hover:shadow-lg hover:shadow-green-100/50 transition-all duration-300">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition" style="background:rgba(114,191,119,.1)">
                    {{ $cat->image ? '🏷️' : substr($cat->name,0,1) }}
                </div>
                <p class="text-sm font-black text-gray-900 group-hover:text-[#3fa348] transition">{{ $cat->name }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $cat->getProductsCount() }} produk</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- PRODUCTS FEED --}}
<section id="feed" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-gray-900">Jualan <span style="color:#72bf77">Tetangga</span></h2>
                <p class="text-gray-400 mt-1 font-medium">Produk langsung dari warga di sekitarmu</p>
            </div>
            <a href="{{ route('buyer.products') }}" class="text-sm font-bold hidden sm:block" style="color:#72bf77">Lihat semua →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($products as $product)
            <div class="product-card group bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-green-100/40 transition-all duration-300">
                <div class="relative aspect-square overflow-hidden bg-gray-50">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                        class="product-img w-full h-full object-cover transition-transform duration-500"
                        onerror="this.src='https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk'">
                    @if($product->discount_percent > 0)
                        <span class="absolute top-3 left-3 px-2 py-1 rounded-xl text-[10px] font-black text-white" style="background:#72bf77">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">🏪 {{ $product->store->name ?? 'Arradea' }}</p>
                    <h3 class="font-black text-gray-900 leading-tight line-clamp-2 mb-2">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->discount_percent > 0)
                                @php $finalPrice = $product->price * (1 - $product->discount_percent/100); @endphp
                                <p class="text-[11px] text-gray-400 line-through">Rp {{ number_format($product->price,0,',','.') }}</p>
                                <p class="font-black text-lg" style="color:#72bf77">Rp {{ number_format($finalPrice,0,',','.') }}</p>
                            @else
                                <p class="font-black text-lg text-gray-900">Rp {{ number_format($product->price,0,',','.') }}</p>
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-400">Stok {{ $product->stock }}</span>
                    </div>
                    <a href="{{ route('buyer.products.show', $product->id) }}"
                        class="buy-btn mt-3 w-full py-2.5 rounded-xl text-sm font-bold text-white text-center block transition"
                        style="background:#72bf77">+ Beli Sekarang</a>
                </div>
            </div>
            @empty
            @for($i=1;$i<=4;$i++)
            <div class="animate-pulse bg-white rounded-3xl border border-gray-100 overflow-hidden">
                <div class="aspect-square bg-gray-100"></div>
                <div class="p-4 space-y-2"><div class="h-4 bg-gray-100 rounded-full w-3/4"></div><div class="h-3 bg-gray-100 rounded-full w-1/2"></div></div>
            </div>
            @endfor
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('buyer.products') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold text-sm border-2 transition hover:text-white" style="border-color:#72bf77;color:#72bf77;hover:background:#72bf77">
                Lihat Semua Produk →
            </a>
        </div>
    </div>
</section>

{{-- CTA BANNER --}}
<section class="py-16 bg-[#f7faf7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl p-8 lg:p-16 text-white text-center" style="background:linear-gradient(135deg,#0f1a11,#1e3a22)">
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest mb-3" style="color:#72bf77">Untuk Warga Arradea</p>
                <h2 class="text-3xl lg:text-4xl font-black tracking-tight mb-4">Punya produk untuk dijual?</h2>
                <p class="text-white/60 mb-8 max-w-lg mx-auto font-medium">Bergabunglah sebagai seller dan mulai berjualan kepada tetangga-tetanggamu.</p>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-gray-900 transition hover:opacity-90" style="background:#72bf77">
                        Daftar Jadi Seller 🚀
                    </a>
                @else
                    @if(!Auth::user()->is_seller && Auth::user()->role !== 'admin')
                        <a href="{{ route('seller.apply') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-gray-900 transition hover:opacity-90" style="background:#72bf77">
                            Buka Toko Sekarang 🚀
                        </a>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
