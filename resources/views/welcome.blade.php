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
    .promo-slider { position: relative; overflow: hidden; border-radius: 0.75rem; }
    .promo-track { display: flex; transition: transform 0.5s ease-in-out; }
    .promo-slide { min-width: 100%; flex-shrink: 0; }
    .promo-dots { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .promo-dot { width: 6px; height: 6px; border-radius: 50%; background: #d1d5db; cursor: pointer; transition: all 0.3s ease; }
    .promo-dot:hover { background: #a3a3a3; }
    .promo-dot.active { width: 20px; border-radius: 3px; background: #72bf77; }
    .promo-nav { position: absolute; top: 50%; transform: translateY(-50%); z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: white; border: 2px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: #72bf77; }
    .promo-nav:hover { background: #72bf77; border-color: #72bf77; color: white; transform: translateY(-50%) scale(1.1); }
    .promo-nav:active { transform: translateY(-50%) scale(0.95); }
    .promo-nav.prev { left: 16px; }
    .promo-nav.next { right: 16px; }
    @media (max-width: 768px) {
        .promo-nav { width: 32px; height: 32px; }
        .promo-nav.prev { left: 8px; }
        .promo-nav.next { right: 8px; }
    }
    @media (max-width: 640px) {
        .promo-nav { width: 28px; height: 28px; }
        .promo-slider { border-radius: 0.5rem; }
    }
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
<section class="py-12 sm:py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 sm:mb-10 lg:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-gray-900">Promo <span class="bg-gradient-to-r from-[#72bf77] to-[#4db85a] bg-clip-text text-transparent">Spesial</span></h2>
            <p class="text-gray-500 mt-2 sm:mt-3 font-medium text-sm sm:text-base lg:text-lg">Jangan lewatkan penawaran terbaik hari ini!</p>
        </div>
        <div class="promo-slider relative fade-up-2">
            {{-- Navigation Buttons --}}
            <button class="promo-nav prev hidden sm:flex" onclick="prevSlide()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="promo-nav next hidden sm:flex" onclick="nextSlide()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="promo-track">
                {{-- Slide 1: Produk Diskon --}}
                @if($discountedProducts->count() > 0)
                <div class="promo-slide">
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-red-100">
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0" style="background:rgba(239,68,68,.15)">
                                🔥
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-black text-gray-900">Diskon Spesial!</h3>
                                <p class="text-xs sm:text-sm text-gray-600 truncate">Hemat hingga {{ $discountedProducts->max('discount_percent') }}%</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
                            @foreach($discountedProducts->take(4) as $product)
                            <a href="{{ route('buyer.products.show', $product->id) }}" class="group bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 sm:hover:-translate-y-1">
                                <div class="relative aspect-square overflow-hidden bg-gray-50">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        onerror="this.src='https://via.placeholder.com/300x300/f0faf1/72bf77?text=Produk'">
                                    <span class="absolute top-1 left-1 sm:top-2 sm:left-2 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[10px] sm:text-xs font-black text-white shadow-lg" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                                        -{{ $product->discount_percent }}%
                                    </span>
                                </div>
                                <div class="p-2 sm:p-3">
                                    <p class="text-[11px] sm:text-xs font-bold text-gray-900 line-clamp-2 mb-1.5 sm:mb-2">{{ $product->name }}</p>
                                    @php $finalPrice = $product->price * (1 - $product->discount_percent/100); @endphp
                                    <p class="text-[9px] sm:text-[10px] text-gray-400 line-through">Rp {{ number_format($product->price,0,',','.') }}</p>
                                    <p class="text-xs sm:text-sm font-black text-red-600">Rp {{ number_format($finalPrice,0,',','.') }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-4 sm:mt-6 text-center">
                            <a href="{{ route('buyer.products') }}?discount=1" class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold text-white transition-all hover:scale-105 active:scale-95" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                                Lihat Semua Diskon
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                @endif

                {{-- Slide 2: Produk Populer --}}
                @if($popularProducts->count() > 0)
                <div class="promo-slide">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-blue-100">
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                            <div class="w-10 sm:w-12 h-10 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0" style="background:rgba(59,130,246,.15)">
                                ⭐
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-black text-gray-900">Paling Laris!</h3>
                                <p class="text-xs sm:text-sm text-gray-600 truncate">Produk favorit warga Arradea</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
                            @foreach($popularProducts->take(4) as $product)
                            <a href="{{ route('buyer.products.show', $product->id) }}" class="group bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 sm:hover:-translate-y-1">
                                <div class="relative aspect-square overflow-hidden bg-gray-50">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        onerror="this.src='https://via.placeholder.com/300x300/f0faf1/72bf77?text=Produk'">
                                    <span class="absolute top-1 left-1 sm:top-2 sm:left-2 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-xs font-black text-white shadow-lg" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                                        🔥 {{ $product->orders_count }}
                                    </span>
                                </div>
                                <div class="p-2 sm:p-3">
                                    <p class="text-[11px] sm:text-xs font-bold text-gray-900 line-clamp-2 mb-1.5 sm:mb-2">{{ $product->name }}</p>
                                    @if($product->discount_percent > 0)
                                        @php $finalPrice = $product->price * (1 - $product->discount_percent/100); @endphp
                                        <p class="text-[9px] sm:text-[10px] text-gray-400 line-through">Rp {{ number_format($product->price,0,',','.') }}</p>
                                        <p class="text-xs sm:text-sm font-black" style="color:#72bf77">Rp {{ number_format($finalPrice,0,',','.') }}</p>
                                    @else
                                        <p class="text-xs sm:text-sm font-black text-gray-900">Rp {{ number_format($product->price,0,',','.') }}</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-4 sm:mt-6 text-center">
                            <a href="{{ route('buyer.products') }}" class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold text-white transition-all hover:scale-105 active:scale-95" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                                Lihat Semua Produk
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Navigation Buttons (Desktop) --}}
            <button class="promo-nav prev hidden md:flex" onclick="prevSlide()" title="Slide sebelumnya">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="promo-nav next hidden md:flex" onclick="nextSlide()" title="Slide berikutnya">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Dots Navigation --}}
            <div class="promo-dots absolute bottom-4 sm:bottom-6 left-0 right-0" id="promoDots"></div>
        </div>
    </div>
</section>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.promo-slide');
const track = document.querySelector('.promo-track');
const dotsContainer = document.getElementById('promoDots');
let autoSlideInterval;
let touchStartX = 0;
let touchEndX = 0;
let isSwiping = false;

// Create dots
slides.forEach((_, index) => {
    const dot = document.createElement('div');
    dot.className = 'promo-dot' + (index === 0 ? ' active' : '');
    dot.onclick = () => goToSlide(index);
    dot.setAttribute('aria-label', `Slide ${index + 1}`);
    dot.setAttribute('role', 'button');
    dot.setAttribute('tabindex', '0');
    dotsContainer.appendChild(dot);
});

const dots = document.querySelectorAll('.promo-dot');

function updateSlider() {
    if (track) {
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}

function nextSlide() {
    if (slides.length > 0) {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlider();
    }
}

function prevSlide() {
    if (slides.length > 0) {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlider();
    }
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
    resetAutoSlide();
}

function startAutoSlide() {
    if (slides.length > 1) {
        autoSlideInterval = setInterval(nextSlide, 6000);
    }
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

// Initialize
updateSlider();
startAutoSlide();

// Event listeners
const slider = document.querySelector('.promo-slider');
if (slider) {
    // Pause auto-slide on hover
    slider.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
    slider.addEventListener('mouseleave', () => startAutoSlide());
    
    // Touch support for mobile
    slider.addEventListener('touchstart', (e) => {
        isSwiping = true;
        touchStartX = e.changedTouches[0].screenX;
        clearInterval(autoSlideInterval);
    }, { passive: true });

    slider.addEventListener('touchmove', () => {
        isSwiping = true;
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
        if (!isSwiping) return;
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        isSwiping = false;
        startAutoSlide();
    }, { passive: true });
}

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }
}

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    if (slider && slider.contains(document.activeElement)) {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    }
});
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
