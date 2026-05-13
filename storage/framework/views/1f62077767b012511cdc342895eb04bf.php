<?php $__env->startSection('title', 'Arradea — Marketplace Warga'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/welcome-page.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    use Illuminate\Support\Facades\Cache;
    
    // Cache hanya IDs produk terbaru (5 menit), fetch models fresh - ambil 5 saja
    $productIds = Cache::remember('home:products:latest:ids', 300, function () {
        return \App\Models\Product::whereHas('store.user', function ($userQuery) {
            $userQuery->where('is_seller', true);
        })
        ->whereHas('store', function ($storeQuery) {
            $storeQuery->where('status', 'active');
        })
        ->latest()
        ->take(5)
        ->pluck('id')
        ->toArray();
    });
    $products = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $productIds)
        ->get();
    
    // Cache hanya IDs produk dengan diskon (5 menit), fetch models fresh - ambil 5 saja
    $discountedIds = Cache::remember('home:products:discounted:ids', 300, function () {
        return \App\Models\Product::whereHas('store.user', function ($userQuery) {
            $userQuery->where('is_seller', true);
        })
        ->whereHas('store', function ($storeQuery) {
            $storeQuery->where('status', 'active');
        })
        ->where('discount_percent', '>', 0)
        ->orderBy('discount_percent', 'desc')
        ->take(5)
        ->pluck('id')
        ->toArray();
    });
    $discountedProducts = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $discountedIds)
        ->get();
    
    // Cache hanya IDs produk populer (10 menit), fetch models fresh - ambil 5 saja
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
        ->take(5)
        ->pluck('id')
        ->toArray();
    });
    $popularProducts = \App\Models\Product::with(['store:id,name', 'category:id,name'])
        ->whereIn('id', $popularIds)
        ->get();
?>

<style>
    /* Advanced Animations */
    @keyframes float { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-20px) rotate(2deg)} }
    @keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
    @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
    @keyframes shimmer { 0%{background-position:200% center} 100%{background-position:-200% center} }
    @keyframes slideInLeft { from{opacity:0;transform:translateX(-40px)} to{opacity:1;transform:translateX(0)} }
    @keyframes slideInRight { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(32px)} to{opacity:1;transform:translateY(0)} }
    @keyframes scaleIn { from{opacity:0;transform:scale(0.9)} to{opacity:1;transform:scale(1)} }
    @keyframes rotate { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    @keyframes glow { 0%,100%{box-shadow:0 0 20px rgba(114,191,119,.3)} 50%{box-shadow:0 0 40px rgba(114,191,119,.6)} }
    
    .float { animation: float 6s ease-in-out infinite; }
    .float-slow { animation: floatSlow 8s ease-in-out infinite; }
    .pulse { animation: pulse 3s ease-in-out infinite; }
    .shimmer { 
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.8), transparent);
        background-size: 200% 100%;
        animation: shimmer 3s infinite;
    }
    .slide-in-left { animation: slideInLeft .8s cubic-bezier(0.4,0,0.2,1) both; }
    .slide-in-right { animation: slideInRight .8s cubic-bezier(0.4,0,0.2,1) both; }
    .fade-up { animation: fadeUp .7s cubic-bezier(0.4,0,0.2,1) both; }
    .scale-in { animation: scaleIn .6s cubic-bezier(0.4,0,0.2,1) both; }
    .glow { animation: glow 2s ease-in-out infinite; }
    
    /* Premium Product Cards */
    .product-card { 
        transition: all .4s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
        transition: left .6s;
    }
    .product-card:hover::before { left: 100%; }
    .product-card:hover { 
        transform: translateY(-8px) scale(1.02); 
        box-shadow: 0 24px 48px rgba(114,191,119,.2);
    }
    .product-card:hover .product-img { transform: scale(1.1) rotate(2deg); }
    .product-img { 
        transition: transform .6s cubic-bezier(0.4,0,0.2,1);
        will-change: transform;
    }
    
    /* Hero Blobs with Animation */
    .hero-blob { 
        position:absolute; 
        border-radius:50%; 
        filter:blur(120px); 
        pointer-events:none; 
        opacity:.12;
        animation: float 10s ease-in-out infinite;
    }
    .hero-blob-2 { animation-delay: -5s; animation-duration: 15s; }
    
    /* Premium Glass Effect */
    .glass-card { 
        background:rgba(255,255,255,0.8); 
        backdrop-filter:blur(24px) saturate(180%); 
        -webkit-backdrop-filter:blur(24px) saturate(180%); 
        border:1px solid rgba(114,191,119,0.15);
        transition: all .3s cubic-bezier(0.4,0,0.2,1);
    }
    .glass-card:hover {
        background:rgba(255,255,255,0.9);
        border-color: rgba(114,191,119,0.3);
        transform: translateY(-2px);
    }
    
    /* Interactive Buttons */
    .btn-premium {
        position: relative;
        overflow: hidden;
        transition: all .3s cubic-bezier(0.4,0,0.2,1);
    }
    .btn-premium::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,.3);
        transform: translate(-50%, -50%);
        transition: width .6s, height .6s;
    }
    .btn-premium:hover::before {
        width: 300px;
        height: 300px;
    }
    .btn-premium:active {
        transform: scale(0.95);
    }
    
    /* Scroll Container Premium */
    .scroll-container { 
        overflow-x: auto; 
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #72bf77 transparent;
        margin-left: -1rem;
        margin-right: -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
        scroll-behavior: smooth;
    }
    .scroll-container::-webkit-scrollbar { height: 6px; }
    .scroll-container::-webkit-scrollbar-track { background: transparent; }
    .scroll-container::-webkit-scrollbar-thumb { 
        background: linear-gradient(90deg, #72bf77, #4db85a); 
        border-radius: 10px; 
    }
    .scroll-container::-webkit-scrollbar-thumb:hover { background: #4db85a; }
    .scroll-content { 
        display: flex; 
        gap: 1rem; 
        padding-bottom: 0.5rem;
    }
    .scroll-item { 
        flex: 0 0 auto; 
        width: 180px;
    }
    
    /* Feature Cards Interactive */
    .feature-card {
        transition: all .4s cubic-bezier(0.4,0,0.2,1);
        cursor: pointer;
    }
    .feature-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 20px 40px rgba(114,191,119,.15);
    }
    .feature-card:hover .icon-container {
        transform: scale(1.1) rotate(5deg);
    }
    .icon-container {
        transition: transform .4s cubic-bezier(0.4,0,0.2,1);
    }
    
    /* Stats Counter Animation */
    .stat-number {
        background: linear-gradient(135deg, #72bf77, #4db85a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
        font-size: 2.5rem;
        transition: transform .3s;
    }
    .stat-number:hover {
        transform: scale(1.1);
    }
    
    /* Testimonial Cards */
    .testimonial-card {
        transition: all .4s cubic-bezier(0.4,0,0.2,1);
        cursor: pointer;
    }
    .testimonial-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 20px 40px rgba(114,191,119,.18);
    }
    .testimonial-card:hover .testimonial-avatar {
        transform: scale(1.15) rotate(5deg);
    }
    .testimonial-avatar {
        transition: transform .4s cubic-bezier(0.4,0,0.2,1);
    }
    
    /* Gradient Text Animation */
    .gradient-text {
        background: linear-gradient(90deg, #72bf77, #4db85a, #72bf77);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 3s linear infinite;
    }
    
    /* Floating Cards in Hero */
    .floating-card {
        transition: all .3s cubic-bezier(0.4,0,0.2,1);
    }
    .floating-card:hover {
        transform: scale(1.05) translateY(-4px);
        box-shadow: 0 16px 32px rgba(114,191,119,.25);
    }
    
    @media (min-width: 640px) {
        .scroll-container {
            margin-left: 0;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
        }
        .scroll-item { width: 220px; }
        .stat-number { font-size: 3rem; }
    }
    @media (min-width: 768px) {
        .scroll-item { width: 240px; }
        .stat-number { font-size: 3.5rem; }
    }
</style>


<section data-aos="fade-up" class="relative overflow-hidden min-h-[75vh] sm:min-h-[80vh] lg:min-h-[85vh] flex items-center" style="background:linear-gradient(to bottom,#ffffff 0%,#f7faf7 100%)">
    
    <div class="hero-blob w-[600px] h-[600px] -top-40 -right-40" style="background:#72bf77"></div>
    <div class="hero-blob hero-blob-2 w-96 h-96 bottom-20 -left-24" style="background:#4db85a"></div>
    <div class="hero-blob w-64 h-64 top-1/2 right-1/4 opacity-10" style="background:#f59e0b"></div>

    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 lg:gap-16 items-center">

            
            <div data-aos="fade-right" data-aos-delay="100" class="space-y-4 sm:space-y-6 lg:space-y-8">
                <div class="slide-in-left inline-flex items-center gap-2.5 px-4 py-2.5 rounded-full text-xs font-bold glass-card shadow-lg glow">
                    <span class="text-base sm:text-lg pulse">🏘️</span>
                    <span class="gradient-text">Pasar Warga Arradea</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black tracking-tighter leading-[0.9] text-gray-900 slide-in-left" style="animation-delay:.1s">
                    Segar<br>
                    <span class="gradient-text">Dekat</span><br>
                    Lengkap.
                </h1>

                <p class="text-sm sm:text-base lg:text-lg text-gray-600 leading-relaxed max-w-lg font-medium slide-in-left" style="animation-delay:.2s">
                    Dukung jualan tetangga! Dari makanan ibu-ibu komplek sampai jasa profesional, semua ada di sini.
                </p>

                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 slide-in-left" style="animation-delay:.3s">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('buyer.products')); ?>" class="btn-premium group px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-white text-sm sm:text-base transition-all duration-300 hover:-translate-y-2 active:scale-95 text-center relative z-10" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 16px 48px rgba(114,191,119,.4)">
                            <span class="flex items-center justify-center gap-2 relative z-10">
                                Belanja Sekarang
                                <svg class="w-4 h-4 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </span>
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-premium px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-gray-700 text-sm sm:text-base glass-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center relative">
                            <span class="relative z-10">Gabung Seller →</span>
                        </a>
                    <?php else: ?>
                        <?php if(Auth::user()->role === 'admin'): ?>
                            <a href="/admin/dashboard" class="btn-premium px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-gray-700 text-sm sm:text-base glass-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center relative">
                                <span class="relative z-10">Dashboard Admin →</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('buyer.products')); ?>" class="btn-premium group px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-white text-sm sm:text-base transition-all duration-300 hover:-translate-y-2 active:scale-95 text-center relative z-10" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 16px 48px rgba(114,191,119,.4)">
                                <span class="flex items-center justify-center gap-2 relative z-10">
                                    Belanja Sekarang
                                    <svg class="w-4 h-4 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </span>
                            </a>
                            <?php
                                $welcomeActiveMode = Auth::user()->getActiveMode();
                                $dashboardRoute = ($welcomeActiveMode === 'seller' && Auth::user()->canSwitchToSellerMode())
                                    ? route('seller.dashboard')
                                    : route('buyer.dashboard');
                            ?>
                            <a href="<?php echo e($dashboardRoute); ?>" class="btn-premium px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-gray-700 text-sm sm:text-base glass-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center relative">
                                <span class="relative z-10">Dashboard Saya →</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            
            <div data-aos="fade-left" data-aos-delay="200" class="relative hidden lg:block h-[450px]">
                
                <div class="absolute inset-0 flex items-center justify-center">
                    <div data-aos="zoom-in" data-aos-delay="400" class="pulse w-80 h-80 rounded-[4rem] flex items-center justify-center text-9xl shadow-2xl shadow-green-200/50" style="background:linear-gradient(135deg,#f0faf1,#d8f3da)">
                        🏪
                    </div>
                </div>

                
                <div data-aos="fade-down" data-aos-delay="600" class="floating-card float absolute top-8 right-6 glass-card rounded-2xl p-5 shadow-2xl" style="animation-delay:0s">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl pulse" style="background:rgba(114,191,119,.2)">🛒</div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Pesanan Baru!</p>
                            <p class="text-xs text-gray-500 mt-0.5">2 menit lalu</p>
                        </div>
                    </div>
                </div>

                
                <div data-aos="fade-up" data-aos-delay="700" class="floating-card float-slow absolute bottom-12 left-6 glass-card rounded-2xl p-5 shadow-2xl" style="animation-delay:1.5s">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl pulse" style="background:rgba(245,158,11,.2)">⭐</div>
                        <div>
                            <p class="text-sm font-black text-gray-900">Rating Sempurna</p>
                            <p class="text-xs text-gray-500 mt-0.5">Baru direview</p>
                        </div>
                    </div>
                </div>

                
                <div data-aos="fade-left" data-aos-delay="800" class="floating-card float absolute top-1/2 right-3 glass-card rounded-2xl p-4 shadow-2xl" style="animation-delay:.8s">
                    <p class="text-sm font-black gradient-text">💰 Rp 350.000</p>
                    <p class="text-xs text-gray-500 mt-1">Penjualan hari ini</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="welcome-section bg-white py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        
        <?php if($products->isNotEmpty()): ?>
        <div data-aos="fade-up" class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-1">✨ Produk Terbaru</h2>
                    <p class="text-sm text-gray-500">Produk fresh dari tetangga sekitar</p>
                </div>
                <a href="<?php echo e(route('buyer.products')); ?>" class="text-sm font-bold text-[#72bf77] hover:text-[#4db85a] transition-colors whitespace-nowrap">
                    Lihat Semua →
                </a>
            </div>
            
            <div class="scroll-container">
                <div class="scroll-content">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="scroll-item">
                        <a href="<?php echo e(route('buyer.products.show', $product->id)); ?>" class="product-card glass-card rounded-xl overflow-hidden group block h-full">
                            <div class="relative overflow-hidden aspect-square bg-gray-100">
                                <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="product-img w-full h-full object-cover absolute inset-0">
                                <div class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md min-w-[45px] text-center leading-none">
                                    NEW
                                </div>
                                <?php if($product->has_active_discount): ?>
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md leading-none">
                                    -<?php echo e(number_format($product->active_discount_percent)); ?>%
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-gray-500 mb-1 truncate"><?php echo e($product->store->name); ?></p>
                                <h3 class="font-bold text-sm text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]"><?php echo e($product->name); ?></h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <?php if($product->has_active_discount): ?>
                                        <p class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                        <p class="text-sm font-black text-[#72bf77]">Rp <?php echo e(number_format($product->final_price, 0, ',', '.')); ?></p>
                                        <?php else: ?>
                                        <p class="text-sm font-black text-[#72bf77]">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($popularProducts->isNotEmpty()): ?>
        <div data-aos="fade-up" data-aos-delay="100" class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-1">🔥 Paling Laris</h2>
                    <p class="text-sm text-gray-500">Favorit warga Arradea</p>
                </div>
                <a href="<?php echo e(route('buyer.products')); ?>" class="text-sm font-bold text-[#72bf77] hover:text-[#4db85a] transition-colors whitespace-nowrap">
                    Lihat Semua →
                </a>
            </div>
            
            <div class="scroll-container">
                <div class="scroll-content">
                    <?php $__currentLoopData = $popularProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="scroll-item">
                        <a href="<?php echo e(route('buyer.products.show', $product->id)); ?>" class="product-card glass-card rounded-xl overflow-hidden group block h-full">
                            <div class="relative overflow-hidden aspect-square bg-gray-100">
                                <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="product-img w-full h-full object-cover absolute inset-0">
                                <div class="absolute top-2 left-2 bg-orange-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md min-w-[45px] text-center leading-none">
                                    LARIS
                                </div>
                                <?php if($product->has_active_discount): ?>
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md leading-none">
                                    -<?php echo e(number_format($product->active_discount_percent)); ?>%
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-gray-500 mb-1 truncate"><?php echo e($product->store->name); ?></p>
                                <h3 class="font-bold text-sm text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]"><?php echo e($product->name); ?></h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <?php if($product->has_active_discount): ?>
                                        <p class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                        <p class="text-sm font-black text-[#72bf77]">Rp <?php echo e(number_format($product->final_price, 0, ',', '.')); ?></p>
                                        <?php else: ?>
                                        <p class="text-sm font-black text-[#72bf77]">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($discountedProducts->isNotEmpty()): ?>
        <div data-aos="fade-up" data-aos-delay="200" class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-1">💰 Promo Spesial</h2>
                    <p class="text-sm text-gray-500">Penawaran terbaik hari ini</p>
                </div>
                <a href="<?php echo e(route('buyer.products')); ?>" class="text-sm font-bold text-[#72bf77] hover:text-[#4db85a] transition-colors whitespace-nowrap">
                    Lihat Semua →
                </a>
            </div>
            
            <div class="scroll-container">
                <div class="scroll-content">
                    <?php $__currentLoopData = $discountedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="scroll-item">
                        <a href="<?php echo e(route('buyer.products.show', $product->id)); ?>" class="product-card glass-card rounded-xl overflow-hidden group block h-full">
                            <div class="relative overflow-hidden aspect-square bg-gray-100">
                                <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="product-img w-full h-full object-cover absolute inset-0">
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md min-w-[45px] text-center leading-none">
                                    PROMO
                                </div>
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md leading-none">
                                    -<?php echo e(number_format($product->active_discount_percent)); ?>%
                                </div>
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-gray-500 mb-1 truncate"><?php echo e($product->store->name); ?></p>
                                <h3 class="font-bold text-sm text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]"><?php echo e($product->name); ?></h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                        <p class="text-sm font-black text-red-500">Rp <?php echo e(number_format($product->final_price, 0, ',', '.')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>


<section class="welcome-section bg-[#f7faf7] py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div data-aos="zoom-in" class="relative overflow-hidden rounded-2xl sm:rounded-3xl lg:rounded-[2.5rem] p-8 sm:p-12 lg:p-20 text-white text-center shadow-2xl" style="background:linear-gradient(135deg,#0f1a11,#1e3a22,#0f1a11)">
            
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-20 float" style="background:#72bf77;filter:blur(100px)"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full opacity-15 float-slow" style="background:#4db85a;filter:blur(100px)"></div>
            <div class="absolute top-10 right-10 w-32 h-32 rounded-full opacity-10 pulse" style="background:#f59e0b;filter:blur(60px)"></div>
            
            <div class="relative z-10">
                <p class="section-label text-[#72bf77] mb-4 scale-in">Untuk Warga Arradea</p>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight mb-4 sm:mb-6 leading-tight text-white slide-in-left">Punya produk untuk dijual?</h2>
                <p class="text-white/80 mb-8 sm:mb-10 max-w-2xl mx-auto font-medium text-sm sm:text-base lg:text-lg leading-relaxed slide-in-right">Bergabunglah sebagai seller dan mulai berjualan kepada tetangga-tetanggamu. Raih penghasilan tambahan dari rumah.</p>
                <?php if(Auth::guest() || (Auth::check() && Auth::user()->role !== 'admin')): ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('register')); ?>" class="btn-premium inline-flex items-center gap-3 px-8 sm:px-10 py-4 sm:py-5 rounded-2xl font-black text-gray-900 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl active:scale-95 text-sm sm:text-base relative" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 16px 48px rgba(114,191,119,.5)">
                            <span class="relative z-10">Daftar Jadi Seller</span>
                            <span class="text-xl sm:text-2xl pulse relative z-10">🚀</span>
                        </a>
                    <?php else: ?>
                        <?php if(!Auth::user()->is_seller): ?>
                            <a href="<?php echo e(route('seller.apply')); ?>" class="btn-premium inline-flex items-center gap-3 px-8 sm:px-10 py-4 sm:py-5 rounded-2xl font-black text-gray-900 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl active:scale-95 text-sm sm:text-base relative" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 16px 48px rgba(114,191,119,.5)">
                                <span class="relative z-10">Buka Toko Sekarang</span>
                                <span class="text-xl sm:text-2xl pulse relative z-10">🚀</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


<section class="welcome-section bg-white py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div data-aos="fade-up" class="section-header">
            <p class="section-label slide-in-left">Kenapa Arradea?</p>
            <h2 class="section-title scale-in">Belanja Jadi Lebih <span class="gradient-text">Mudah</span></h2>
            <p class="section-description slide-in-right">Platform marketplace khusus warga Arradea dengan fitur lengkap</p>
        </div>

        <div class="welcome-grid welcome-grid-4">
            
            <div data-aos="fade-up" data-aos-delay="100" class="feature-card welcome-card glass-card text-center group cursor-pointer">
                <div class="icon-container mx-auto w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mb-4 pulse" style="background:rgba(114,191,119,.12)">🚀</div>
                <h3 class="card-title font-black text-lg mb-2">Cepat & Praktis</h3>
                <p class="card-description text-sm text-gray-600">Pesan langsung dari tetangga, barang sampai lebih cepat</p>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="200" class="feature-card welcome-card glass-card text-center group cursor-pointer">
                <div class="icon-container mx-auto w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mb-4 pulse" style="background:rgba(59,130,246,.12);animation-delay:.5s">💰</div>
                <h3 class="card-title font-black text-lg mb-2">Harga Terjangkau</h3>
                <p class="card-description text-sm text-gray-600">Tanpa biaya ongkir mahal, harga lebih bersahabat</p>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="300" class="feature-card welcome-card glass-card text-center group cursor-pointer">
                <div class="icon-container mx-auto w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mb-4 pulse" style="background:rgba(245,158,11,.12);animation-delay:1s">🤝</div>
                <h3 class="card-title font-black text-lg mb-2">Dukung Tetangga</h3>
                <p class="card-description text-sm text-gray-600">Bantu UMKM lokal berkembang di lingkungan kita</p>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="400" class="feature-card welcome-card glass-card text-center group cursor-pointer">
                <div class="icon-container mx-auto w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mb-4 pulse" style="background:rgba(34,197,94,.12);animation-delay:1.5s">✅</div>
                <h3 class="card-title font-black text-lg mb-2">Terpercaya</h3>
                <p class="card-description text-sm text-gray-600">Semua seller terverifikasi, transaksi aman</p>
            </div>
        </div>
    </div>
</section>


<section class="welcome-section bg-gradient-to-b from-[#f7faf7] to-white py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div data-aos="fade-up" class="section-header">
            <p class="section-label">Mudah Banget!</p>
            <h2 class="section-title">Cara <span class="bg-gradient-to-r from-[#72bf77] to-[#4db85a] bg-clip-text text-transparent">Belanja</span></h2>
            <p class="section-description">Hanya 3 langkah untuk mendapatkan produk yang kamu inginkan</p>
        </div>

        <div class="welcome-grid welcome-grid-3 max-w-5xl mx-auto relative">
            
            <div class="hidden md:block absolute top-12 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-green-200 to-transparent"></div>

            
            <div data-aos="fade-up" data-aos-delay="100" class="relative text-center">
                <div class="step-container">
                    <div class="step-circle" style="background:linear-gradient(135deg,#f0faf1,#d8f3da)">🔍</div>
                    <div class="step-badge" style="background:#72bf77">1</div>
                </div>
                <h3 class="step-title">Cari Produk</h3>
                <p class="step-description">Temukan produk yang kamu butuhkan dari seller terdekat</p>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="200" class="relative text-center">
                <div class="step-container">
                    <div class="step-circle" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">🛒</div>
                    <div class="step-badge" style="background:#f59e0b">2</div>
                </div>
                <h3 class="step-title">Pesan & Bayar</h3>
                <p class="step-description">Tambahkan ke keranjang dan lakukan pembayaran dengan mudah</p>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="300" class="relative text-center">
                <div class="step-container">
                    <div class="step-circle" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe)">📦</div>
                    <div class="step-badge" style="background:#3b82f6">3</div>
                </div>
                <h3 class="step-title">Terima Barang</h3>
                <p class="step-description">Barang diantar langsung ke rumahmu, cepat dan aman</p>
            </div>
        </div>
    </div>
</section>


<section class="welcome-section bg-white py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div data-aos="zoom-in" class="glass-card rounded-3xl lg:rounded-[2rem] p-10 sm:p-12 lg:p-16 shadow-2xl relative overflow-hidden">
            
            <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 rounded-full opacity-10" style="background:#4db85a;filter:blur(60px)"></div>
            
            <div class="welcome-grid welcome-grid-4 relative z-10">
                
                <div data-aos="fade-up" data-aos-delay="100" class="text-center group cursor-pointer">
                    <div class="stat-number mb-2 transition-transform duration-300"><?php echo e(\App\Models\Product::count()); ?>+</div>
                    <p class="stat-label text-gray-600 font-semibold">Produk Tersedia</p>
                </div>

                
                <div data-aos="fade-up" data-aos-delay="200" class="text-center group cursor-pointer">
                    <div class="stat-number mb-2 transition-transform duration-300"><?php echo e(\App\Models\User::where('is_seller', true)->count()); ?>+</div>
                    <p class="stat-label text-gray-600 font-semibold">Seller Aktif</p>
                </div>

                
                <div data-aos="fade-up" data-aos-delay="300" class="text-center group cursor-pointer">
                    <div class="stat-number mb-2 transition-transform duration-300"><?php echo e(\App\Models\Order::count()); ?>+</div>
                    <p class="stat-label text-gray-600 font-semibold">Transaksi Sukses</p>
                </div>

                
                <div data-aos="fade-up" data-aos-delay="400" class="text-center group cursor-pointer">
                    <div class="stat-number mb-2 transition-transform duration-300">100%</div>
                    <p class="stat-label text-gray-600 font-semibold">Kepuasan Warga</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="welcome-section bg-gradient-to-b from-white to-[#f7faf7] py-16">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="section-header scale-in">
            <p class="section-label">Kata Mereka</p>
            <h2 class="section-title">Testimoni <span class="gradient-text">Warga</span></h2>
            <p class="section-description">Pengalaman nyata dari pengguna Arradea Marketplace</p>
        </div>

        <div class="welcome-grid welcome-grid-3">
            
            <div data-aos="fade-up" data-aos-delay="100" class="testimonial-card glass-card p-6 rounded-2xl hover:shadow-2xl transition-all duration-400">
                <div class="testimonial-stars mb-4 flex gap-1">
                    <span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span>
                </div>
                <p class="testimonial-text text-gray-700 mb-6 leading-relaxed">"Belanja jadi gampang banget! Tinggal pesan dari tetangga, barang langsung sampai. Harga juga lebih murah dari marketplace lain."</p>
                <div class="testimonial-author flex items-center gap-3">
                    <div class="testimonial-avatar w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg" style="background:linear-gradient(135deg,#72bf77,#4db85a)">B</div>
                    <div>
                        <p class="testimonial-name font-black text-gray-900">Bu Siti</p>
                        <p class="testimonial-role text-sm text-gray-500">Pembeli Aktif</p>
                    </div>
                </div>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="200" class="testimonial-card glass-card p-6 rounded-2xl hover:shadow-2xl transition-all duration-400">
                <div class="testimonial-stars mb-4 flex gap-1">
                    <span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span>
                </div>
                <p class="testimonial-text text-gray-700 mb-6 leading-relaxed">"Sebagai seller, Arradea sangat membantu! Bisa jualan ke tetangga sendiri, orderan juga banyak. Recommended!"</p>
                <div class="testimonial-author flex items-center gap-3">
                    <div class="testimonial-avatar w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg" style="background:linear-gradient(135deg,#f59e0b,#d97706)">P</div>
                    <div>
                        <p class="testimonial-name font-black text-gray-900">Pak Budi</p>
                        <p class="testimonial-role text-sm text-gray-500">Seller Toko Sayur</p>
                    </div>
                </div>
            </div>

            
            <div data-aos="fade-up" data-aos-delay="300" class="testimonial-card glass-card p-6 rounded-2xl hover:shadow-2xl transition-all duration-400">
                <div class="testimonial-stars mb-4 flex gap-1">
                    <span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span><span class="text-xl">⭐</span>
                </div>
                <p class="testimonial-text text-gray-700 mb-6 leading-relaxed">"Platform-nya user friendly, mudah dipakai. Chat sama seller juga responsif. Pokoknya top deh!"</p>
                <div class="testimonial-author flex items-center gap-3">
                    <img src="/icons/logo-arradea.png" alt="Arradea" class="testimonial-avatar w-12 h-12 rounded-xl object-cover shadow-lg" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                    <div>
                        <p class="testimonial-name font-black text-gray-900">Andi</p>
                        <p class="testimonial-role text-sm text-gray-500">Pembeli Setia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php if (isset($component)) { $__componentOriginala2343803a0dddccb01ca689ae5a05a65 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala2343803a0dddccb01ca689ae5a05a65 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pwa-install-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pwa-install-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala2343803a0dddccb01ca689ae5a05a65)): ?>
<?php $attributes = $__attributesOriginala2343803a0dddccb01ca689ae5a05a65; ?>
<?php unset($__attributesOriginala2343803a0dddccb01ca689ae5a05a65); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2343803a0dddccb01ca689ae5a05a65)): ?>
<?php $component = $__componentOriginala2343803a0dddccb01ca689ae5a05a65; ?>
<?php unset($__componentOriginala2343803a0dddccb01ca689ae5a05a65); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradeaaaa\resources\views/welcome.blade.php ENDPATH**/ ?>