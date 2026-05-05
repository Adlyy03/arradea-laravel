@extends('layouts.app')
@section('title', 'Arradea — Marketplace Warga')

@section('content')
@php
    use Illuminate\Support\Facades\Cache;
    
    // Cache hanya IDs produk terbaru (5 menit), fetch models fresh
    $productIds = Cache::remember('home:products:latest:ids', 300, function () {
        return \App\Models\Product::whereHas('store.user', function ($userQuery) {
            $userQuery->where('is_seller', true);
        })
        ->whereHas('store', function ($storeQuery) {
            $storeQuery->where('status', 'active');
        })
        ->latest()
        ->take(12)
        ->pluck('id')
        ->toArray();
    });
    $products = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $productIds)
        ->get();
    
    // Cache hanya IDs produk dengan diskon (5 menit), fetch models fresh
    $discountedIds = Cache::remember('home:products:discounted:ids', 300, function () {
        return \App\Models\Product::whereHas('store.user', function ($userQuery) {
            $userQuery->where('is_seller', true);
        })
        ->whereHas('store', function ($storeQuery) {
            $storeQuery->where('status', 'active');
        })
        ->where('discount_percent', '>', 0)
        ->orderBy('discount_percent', 'desc')
        ->take(8)
        ->pluck('id')
        ->toArray();
    });
    $discountedProducts = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $discountedIds)
        ->get();
    
    // Cache hanya IDs produk populer (10 menit), fetch models fresh
    $popularIds = Cache::remember('home:products:popular:ids', 600, function () {
        return \App\Models\Product::whereHas('store.user', function ($userQuery) {
            $userQuery->where('is_seller', true);
        })
        ->whereHas('store', function ($storeQuery) {
            $storeQuery->where('status', 'active');
        })
        ->withCount('orders')
        ->having('orders_count', '>', 0)
        ->orderBy('orders_count', 'desc')
        ->take(8)
        ->pluck('id')
        ->toArray();
    });
    $popularProducts = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $popularIds)
        ->get();
@endphp

<style>
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(32px)} to{opacity:1;transform:translateY(0)} }
    @keyframes scaleIn { from{opacity:0;transform:scale(0.92)} to{opacity:1;transform:scale(1)} }
    .float { animation: float 5s ease-in-out infinite; }
    .fade-up { animation: fadeUp .7s cubic-bezier(0.4,0,0.2,1) both; }
    .fade-up-2 { animation: fadeUp .7s cubic-bezier(0.4,0,0.2,1) .15s both; }
    .fade-up-3 { animation: fadeUp .7s cubic-bezier(0.4,0,0.2,1) .3s both; }
    .scale-in { animation: scaleIn .5s cubic-bezier(0.4,0,0.2,1) both; }
    .product-card { transition: all .35s cubic-bezier(0.4,0,0.2,1); }
    .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(114,191,119,.18); }
    .product-card:hover .product-img { transform: scale(1.08); }
    .product-card:hover .buy-btn { opacity:1; transform:translateY(0); }
    .product-img { transition: transform .5s cubic-bezier(0.4,0,0.2,1); }
    .buy-btn { opacity:0; transform:translateY(12px); transition:all .35s cubic-bezier(0.4,0,0.2,1); }
    .hero-blob { position:absolute; border-radius:50%; filter:blur(100px); pointer-events:none; opacity:.15; }
    .glass-card { background:rgba(255,255,255,0.75); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(114,191,119,0.12); }
    .category-card { transition: all .3s cubic-bezier(0.4,0,0.2,1); }
    .category-card:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 28px rgba(114,191,119,.15); }
    
    /* Slider Styles */
    .promo-slider { position: relative; }
    .promo-overflow { overflow: hidden; border-radius: 1.25rem; }
    .promo-track { display: flex; transition: transform 0.45s cubic-bezier(0.4,0,0.2,1); }
    .promo-slide { min-width: 100%; flex-shrink: 0; }
    .promo-dots { display: flex; gap: 6px; justify-content: center; }
    .promo-dot { width: 6px; height: 6px; border-radius: 50%; background: #d1d5db; cursor: pointer; transition: all 0.3s ease; border: none; padding: 0; }
    .promo-dot.active { width: 22px; border-radius: 3px; background: #72bf77; }
    .promo-nav { width: 34px; height: 34px; border-radius: 50%; background: white; border: 1.5px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08); color: #6b7280; flex-shrink: 0; }
    .promo-nav:hover { background: #72bf77; border-color: #72bf77; color: white; }
</style>

{{-- HERO --}}
<section class="relative overflow-hidden min-h-[85vh] sm:min-h-[88vh] flex items-center" style="background:linear-gradient(to bottom,#ffffff 0%,#f7faf7 100%)">
    {{-- Blobs --}}
    <div class="hero-blob w-[500px] h-[500px] -top-32 -right-32" style="background:#72bf77"></div>
    <div class="hero-blob w-80 h-80 bottom-20 -left-20" style="background:#4db85a"></div>

    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-12 sm:py-20 lg:py-28 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-24 items-center">

            {{-- Left --}}
            <div class="space-y-6 sm:space-y-10 fade-up">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-full text-xs font-bold glass-card shadow-sm">
                    <span class="text-base sm:text-lg">🏘️</span>
                    <span style="color:#3fa348">Pasar Warga Arradea</span>
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-8xl font-black tracking-tighter leading-[0.88] text-gray-900">
                    Segar<br>
                    <span class="bg-gradient-to-r from-[#72bf77] to-[#4db85a] bg-clip-text text-transparent">Dekat</span><br>
                    Lengkap.
                </h1>

                <p class="text-base sm:text-xl text-gray-600 leading-relaxed max-w-lg font-medium">
                    Dukung jualan tetangga! Dari makanan ibu-ibu komplek sampai jasa profesional, semua ada di sini.
                </p>

                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-5">
                    <a href="#feed" class="group px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-bold text-white text-sm sm:text-base transition-all duration-300 hover:-translate-y-1 active:scale-95 text-center" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 12px 40px rgba(114,191,119,.35)">
                        <span class="flex items-center justify-center gap-2">
                            Belanja Sekarang
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-bold text-gray-700 text-sm sm:text-base glass-card hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 text-center">
                            Gabung Seller →
                        </a>
                    @else
                        <a href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('buyer.dashboard') }}" class="px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-bold text-gray-700 text-sm sm:text-base glass-card hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 text-center">
                            Dashboard Saya →
                        </a>
                    @endguest
                </div>
            </div>

            {{-- Right: Floating Cards --}}
            <div class="relative hidden lg:block h-[520px] fade-up-2">
                {{-- Main visual --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-80 h-80 rounded-[5rem] flex items-center justify-center text-9xl shadow-2xl shadow-green-200/40 scale-in" style="background:linear-gradient(135deg,#f0faf1,#d8f3da)">
                        🏪
                    </div>
                </div>

                {{-- Floating card 1 --}}
                <div class="float absolute top-12 right-8 glass-card rounded-2xl p-5 shadow-xl" style="animation-delay:0s">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(114,191,119,.15)">🛒</div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Pesanan Baru!</p>
                            <p class="text-xs text-gray-400 mt-0.5">2 menit yang lalu</p>
                        </div>
                    </div>
                </div>

                {{-- Floating card 2 --}}
                <div class="float absolute bottom-16 left-8 glass-card rounded-2xl p-5 shadow-xl" style="animation-delay:1.5s">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(245,158,11,.15)">⭐</div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Rating Sempurna</p>
                            <p class="text-xs text-gray-400 mt-0.5">Baru direview</p>
                        </div>
                    </div>
                </div>

                {{-- Floating card 3 --}}
                <div class="float absolute top-1/2 right-4 glass-card rounded-2xl p-4 shadow-xl" style="animation-delay:.8s">
                    <p class="text-sm font-black text-gray-700">💰 Rp 350.000</p>
                    <p class="text-xs text-gray-400 mt-0.5">Penjualan hari ini</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PROMO BANNER SLIDER --}}
@if($discountedProducts->count() > 0 || $popularProducts->count() > 0)
<section class="py-8 sm:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header + Nav --}}
        <div class="flex items-center justify-between mb-4 sm:mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-black tracking-tight text-gray-900">Promo <span class="bg-gradient-to-r from-[#72bf77] to-[#4db85a] bg-clip-text text-transparent">Spesial</span></h2>
                <p class="text-gray-400 text-[10px] sm:text-xs font-medium mt-0.5">Penawaran terbaik hari ini</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="promo-nav" onclick="prevSlide()" aria-label="Sebelumnya">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="promo-dots" id="promoDots"></div>
                <button class="promo-nav" onclick="nextSlide()" aria-label="Berikutnya">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- Slider --}}
        <div class="promo-slider fade-up-2">
            <div class="promo-overflow">
                <div class="promo-track">

                    {{-- Slide 1: Diskon --}}
                    @if($discountedProducts->count() > 0)
                    <div class="promo-slide">
                        <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-100 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🔥</span>
                                    <p class="text-xs sm:text-sm font-black text-gray-900">Diskon Spesial — hemat hingga {{ $discountedProducts->max('discount_percent') }}%</p>
                                </div>
                                <a href="{{ route('buyer.products') }}" class="text-[10px] font-bold text-red-500 hover:text-red-600 transition whitespace-nowrap hidden sm:block">Lihat semua →</a>
                            </div>
                            {{-- Horizontal scroll list --}}
                            <div class="grid grid-cols-4 gap-2 sm:gap-3">
                                @foreach($discountedProducts->take(4) as $product)
                                <a href="{{ route('buyer.products.show', $product->id) }}" class="group bg-white rounded-lg overflow-hidden border border-gray-100 hover:border-red-200 hover:shadow-sm transition-all duration-200">
                                    <div class="relative overflow-hidden bg-gray-50" style="height:80px">
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            onerror="this.src='https://via.placeholder.com/200x200/fff5f5/ef4444?text=+'">
                                        <span class="absolute top-1 left-1 px-1 py-0.5 rounded text-[8px] font-black text-white leading-none" style="background:#ef4444">-{{ $product->discount_percent }}%</span>
                                    </div>
                                    <div class="p-1.5">
                                        <p class="text-[9px] sm:text-[10px] font-semibold text-gray-800 line-clamp-1 leading-tight">{{ $product->name }}</p>
                                        @php $fp = $product->price * (1 - $product->discount_percent / 100); @endphp
                                        <p class="text-[8px] text-gray-400 line-through leading-none mt-0.5">Rp {{ number_format($product->price,0,',','.') }}</p>
                                        <p class="text-[9px] sm:text-[10px] font-black text-red-600 leading-tight">Rp {{ number_format($fp,0,',','.') }}</p>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Slide 2: Populer --}}
                    @if($popularProducts->count() > 0)
                    <div class="promo-slide">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">⭐</span>
                                    <p class="text-xs sm:text-sm font-black text-gray-900">Paling Laris — favorit warga Arradea</p>
                                </div>
                                <a href="{{ route('buyer.products') }}" class="text-[10px] font-bold text-blue-500 hover:text-blue-600 transition whitespace-nowrap hidden sm:block">Lihat semua →</a>
                            </div>
                            <div class="grid grid-cols-4 gap-2 sm:gap-3">
                                @foreach($popularProducts->take(4) as $product)
                                <a href="{{ route('buyer.products.show', $product->id) }}" class="group bg-white rounded-lg overflow-hidden border border-gray-100 hover:border-blue-200 hover:shadow-sm transition-all duration-200">
                                    <div class="relative overflow-hidden bg-gray-50" style="height:80px">
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            onerror="this.src='https://via.placeholder.com/200x200/eff6ff/3b82f6?text=+'">
                                        <span class="absolute top-1 left-1 px-1 py-0.5 rounded text-[8px] font-black text-white leading-none" style="background:#3b82f6">🔥{{ $product->orders_count }}x</span>
                                    </div>
                                    <div class="p-1.5">
                                        <p class="text-[9px] sm:text-[10px] font-semibold text-gray-800 line-clamp-1 leading-tight">{{ $product->name }}</p>
                                        @if($product->discount_percent > 0)
                                            @php $fp = $product->price * (1 - $product->discount_percent / 100); @endphp
                                            <p class="text-[8px] text-gray-400 line-through leading-none mt-0.5">Rp {{ number_format($product->price,0,',','.') }}</p>
                                            <p class="text-[9px] sm:text-[10px] font-black leading-tight" style="color:#72bf77">Rp {{ number_format($fp,0,',','.') }}</p>
                                        @else
                                            <p class="text-[9px] sm:text-[10px] font-black text-gray-900 leading-tight mt-0.5">Rp {{ number_format($product->price,0,',','.') }}</p>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Dots mobile --}}
            <div class="flex sm:hidden justify-center mt-2.5 gap-1.5" id="promoDotsMobile"></div>
        </div>

    </div>
</section>

<script>
(function() {
    let cur = 0;
    const track   = document.querySelector('.promo-track');
    const slides  = document.querySelectorAll('.promo-slide');
    const dotsEl  = document.getElementById('promoDots');
    const dotsElM = document.getElementById('promoDotsMobile');
    let timer;

    if (!slides.length || !track) return;

    // Build dots for both containers
    [dotsEl, dotsElM].forEach(function(container) {
        if (!container) return;
        slides.forEach(function(_, i) {
            const d = document.createElement('button');
            d.className = 'promo-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', 'Slide ' + (i + 1));
            d.onclick = function() { go(i); reset(); };
            container.appendChild(d);
        });
    });

    function update() {
        track.style.transform = 'translateX(-' + (cur * 100) + '%)';
        document.querySelectorAll('.promo-dot').forEach(function(d, i) {
            d.classList.toggle('active', i % slides.length === cur);
        });
    }

    function go(n) { cur = (n + slides.length) % slides.length; update(); }
    window.nextSlide = function() { go(cur + 1); reset(); };
    window.prevSlide = function() { go(cur - 1); reset(); };

    function start() { if (slides.length > 1) timer = setInterval(function() { go(cur + 1); }, 5500); }
    function reset() { clearInterval(timer); start(); }

    update();
    start();

    // Touch swipe
    let tx = 0;
    const wrap = document.querySelector('.promo-overflow');
    if (wrap) {
        wrap.addEventListener('touchstart', function(e) { tx = e.changedTouches[0].screenX; clearInterval(timer); }, { passive: true });
        wrap.addEventListener('touchend', function(e) {
            const diff = tx - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 40) diff > 0 ? go(cur + 1) : go(cur - 1);
            start();
        }, { passive: true });
    }
})();
</script>
@endif


{{-- CTA BANNER --}}
<section class="py-12 sm:py-20 bg-[#f7faf7]">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] sm:rounded-[2.5rem] p-8 sm:p-10 lg:p-20 text-white text-center shadow-2xl" style="background:linear-gradient(135deg,#0f1a11,#1e3a22,#0f1a11)">
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full opacity-15" style="background:#72bf77;filter:blur(80px)"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full opacity-10" style="background:#4db85a;filter:blur(80px)"></div>
            <div class="relative z-10 fade-up">
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest mb-3 sm:mb-4" style="color:#72bf77">Untuk Warga Arradea</p>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight mb-4 sm:mb-5 leading-tight">Punya produk untuk dijual?</h2>
                <p class="text-white/70 mb-8 sm:mb-10 max-w-2xl mx-auto font-medium text-sm sm:text-lg leading-relaxed">Bergabunglah sebagai seller dan mulai berjualan kepada tetangga-tetanggamu. Raih penghasilan tambahan dari rumah.</p>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 sm:px-10 py-3.5 sm:py-5 rounded-2xl font-bold text-gray-900 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl active:scale-95 text-base sm:text-lg" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 12px 40px rgba(114,191,119,.4)">
                        Daftar Jadi Seller
                        <span class="text-xl sm:text-2xl">🚀</span>
                    </a>
                @else
                    @if(!Auth::user()->is_seller && Auth::user()->role !== 'admin')
                        <a href="{{ route('seller.apply') }}" class="inline-flex items-center gap-2 px-8 sm:px-10 py-3.5 sm:py-5 rounded-2xl font-bold text-gray-900 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl active:scale-95 text-base sm:text-lg" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 12px 40px rgba(114,191,119,.4)">
                            Buka Toko Sekarang
                            <span class="text-xl sm:text-2xl">🚀</span>
                        </a>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
