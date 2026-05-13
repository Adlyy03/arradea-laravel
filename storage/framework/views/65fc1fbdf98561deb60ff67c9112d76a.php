<?php $__env->startSection('title', 'Dashboard Pembeli — Arradea'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stat-card { background:rgba(255,255,255,0.75); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(114,191,119,0.12); transition:all .35s cubic-bezier(0.4,0,0.2,1); }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(114,191,119,.15); border-color:rgba(114,191,119,0.25); }
    .action-card { transition:all .3s cubic-bezier(0.4,0,0.2,1); }
    .action-card:hover { transform:translateY(-3px) scale(1.02); box-shadow:0 12px 28px rgba(114,191,119,.15); }
    .order-row { transition:all .25s cubic-bezier(0.4,0,0.2,1); }
    .order-row:hover { background:rgba(114,191,119,0.04); transform:translateX(4px); }
    
    /* Mobile optimizations - Ultra Compact */
    @media(max-width:1023px){
        .mobile-compact .stat-card { padding:8px !important; border-radius:10px !important; }
        .mobile-compact .stat-number { font-size:20px !important; line-height:1.2 !important; }
        .mobile-compact .stat-label { font-size:10px !important; margin-top:2px !important; }
        .mobile-compact .action-card { padding:8px !important; border-radius:8px !important; }
        .mobile-compact .greeting-title { font-size:16px !important; font-weight:700 !important; }
        .mobile-compact .order-row { padding:8px 10px !important; }
        .mobile-compact .order-text { font-size:12px !important; line-height:1.3 !important; }
        .mobile-compact .order-meta { font-size:10px !important; }
        .mobile-compact .action-card span:first-child { font-size:20px !important; }
        .mobile-compact .action-card span:last-child { font-size:11px !important; }
        .mobile-compact h2 { font-size:12px !important; }
        .mobile-compact .stat-card > div:first-child .w-8,
        .mobile-compact .stat-card > div:first-child .w-12 { width:28px !important; height:28px !important; font-size:16px !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $recentOrders = auth()->user()->orders()->with('store','product')->latest()->take(5)->get();
?>

<div class="space-y-4 fade-up mobile-compact">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="greeting-title text-2xl lg:text-3xl font-black text-gray-900">Halo, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>! <span class="inline-block animate-bounce">👋</span></h1>
            <p class="text-xs lg:text-sm text-gray-500 mt-1 font-medium"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM Y')); ?></p>
        </div>
        <a href="<?php echo e(route('buyer.products')); ?>" class="hidden sm:flex items-center gap-2 px-4 lg:px-6 py-2 lg:py-3 rounded-xl lg:rounded-2xl text-xs lg:text-sm font-bold text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl active:scale-95" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 8px 24px rgba(114,191,119,.3)">
            <svg class="w-3 lg:w-4 h-3 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Mulai Belanja
        </a>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-5">
        <div class="stat-card rounded-xl lg:rounded-3xl p-3 lg:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-2 lg:mb-4">
                <div class="w-7 lg:w-12 h-7 lg:h-12 rounded-lg lg:rounded-2xl flex items-center justify-center text-base lg:text-2xl" style="background:rgba(114,191,119,.12)">📦</div>
                <span class="text-[9px] lg:text-xs font-bold uppercase tracking-wider text-gray-400">Total</span>
            </div>
            <p class="stat-number text-xl lg:text-4xl font-black text-gray-900 mb-0.5 lg:mb-1"><?php echo e($totalOrders); ?></p>
            <p class="stat-label text-[9px] lg:text-xs text-gray-500 font-medium mb-1.5 lg:mb-3">Total Pesanan</p>
            <a href="<?php echo e(route('buyer.orders')); ?>" class="text-[9px] lg:text-xs font-bold inline-flex items-center gap-0.5 lg:gap-1 group" style="color:#72bf77">
                Lihat
                <svg class="w-2 lg:w-3 h-2 lg:h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="stat-card rounded-xl lg:rounded-3xl p-3 lg:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-2 lg:mb-4">
                <div class="w-7 lg:w-12 h-7 lg:h-12 rounded-lg lg:rounded-2xl flex items-center justify-center text-base lg:text-2xl" style="background:rgba(245,158,11,.12)">⏳</div>
                <span class="text-[9px] lg:text-xs font-bold uppercase tracking-wider text-amber-400">Proses</span>
            </div>
            <p class="stat-number text-xl lg:text-4xl font-black text-amber-500 mb-0.5 lg:mb-1"><?php echo e($pendingOrders); ?></p>
            <p class="stat-label text-[9px] lg:text-xs text-gray-500 font-medium">Diproses</p>
        </div>

        <div class="stat-card rounded-xl lg:rounded-3xl p-3 lg:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-2 lg:mb-4">
                <div class="w-7 lg:w-12 h-7 lg:h-12 rounded-lg lg:rounded-2xl flex items-center justify-center text-base lg:text-2xl" style="background:rgba(34,197,94,.12)">✅</div>
                <span class="text-[9px] lg:text-xs font-bold uppercase tracking-wider text-green-400">Selesai</span>
            </div>
            <p class="stat-number text-xl lg:text-4xl font-black text-green-500 mb-0.5 lg:mb-1"><?php echo e($completedOrders); ?></p>
            <p class="stat-label text-[9px] lg:text-xs text-gray-500 font-medium">Selesai</p>
        </div>

        <div class="stat-card rounded-xl lg:rounded-3xl p-3 lg:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-2 lg:mb-4">
                <div class="w-7 lg:w-12 h-7 lg:h-12 rounded-lg lg:rounded-2xl flex items-center justify-center text-base lg:text-2xl" style="background:rgba(114,191,119,.12)">🛒</div>
                <span class="text-[9px] lg:text-xs font-bold uppercase tracking-wider" style="color:#72bf77">Cart</span>
            </div>
            <p class="stat-number text-xl lg:text-4xl font-black mb-0.5 lg:mb-1" style="color:#72bf77"><?php echo e($cartCount); ?></p>
            <p class="stat-label text-[9px] lg:text-xs text-gray-500 font-medium mb-1.5 lg:mb-3">Item</p>
            <a href="<?php echo e(route('buyer.cart')); ?>" class="text-[9px] lg:text-xs font-bold inline-flex items-center gap-0.5 lg:gap-1 group" style="color:#72bf77">
                Checkout
                <svg class="w-2 lg:w-3 h-2 lg:h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    
    <div class="stat-card rounded-xl lg:rounded-3xl p-3 lg:p-6 shadow-lg">
        <h2 class="text-[10px] lg:text-sm font-black text-gray-700 uppercase tracking-wider mb-2.5 lg:mb-5">Aksi Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 lg:gap-4">
            <a href="<?php echo e(route('buyer.products')); ?>" class="action-card flex flex-col items-center gap-1.5 lg:gap-3 p-2.5 lg:p-5 rounded-lg lg:rounded-2xl text-center shadow-sm" style="background:rgba(240,250,241,0.6);border:1px solid rgba(114,191,119,0.15)">
                <span class="text-xl lg:text-3xl">🛍️</span>
                <span class="text-[10px] lg:text-sm font-bold text-gray-700">Belanja</span>
            </a>
            <a href="<?php echo e(route('buyer.cart')); ?>" class="action-card flex flex-col items-center gap-1.5 lg:gap-3 p-2.5 lg:p-5 rounded-lg lg:rounded-2xl text-center shadow-sm relative" style="background:rgba(255,247,237,0.6);border:1px solid rgba(254,215,170,0.4)">
                <span class="text-xl lg:text-3xl">🛒</span>
                <span class="text-[10px] lg:text-sm font-bold text-gray-700">Keranjang</span>
                <?php if($cartCount > 0): ?><span class="absolute top-1.5 lg:top-3 right-1.5 lg:right-3 px-1 lg:px-2 py-0.5 rounded-full text-[8px] lg:text-xs font-black text-white" style="background:#ea580c"><?php echo e($cartCount); ?></span><?php endif; ?>
            </a>
            <a href="<?php echo e(route('buyer.orders')); ?>" class="action-card flex flex-col items-center gap-1.5 lg:gap-3 p-2.5 lg:p-5 rounded-lg lg:rounded-2xl text-center shadow-sm" style="background:rgba(239,246,255,0.6);border:1px solid rgba(191,219,254,0.4)">
                <span class="text-xl lg:text-3xl">📋</span>
                <span class="text-[10px] lg:text-sm font-bold text-gray-700">Pesanan</span>
            </a>
            <button @click="chatModal=true" class="action-card flex flex-col items-center gap-1.5 lg:gap-3 p-2.5 lg:p-5 rounded-lg lg:rounded-2xl text-center shadow-sm" style="background:rgba(240,253,244,0.6);border:1px solid rgba(187,247,208,0.4)">
                <span class="text-xl lg:text-3xl">💬</span>
                <span class="text-[10px] lg:text-sm font-bold text-gray-700">Chat</span>
            </button>
        </div>
    </div>

    
    <div class="stat-card rounded-2xl lg:rounded-3xl overflow-hidden shadow-lg">
        <div class="flex items-center justify-between px-4 lg:px-6 py-3 lg:py-5 border-b border-gray-100/60">
            <h2 class="text-xs lg:text-sm font-black text-gray-700 uppercase tracking-widest">Pesanan Terbaru</h2>
            <a href="<?php echo e(route('buyer.orders')); ?>" class="text-[10px] lg:text-xs font-bold inline-flex items-center gap-1 group transition" style="color:#72bf77">
                Lihat Semua
                <svg class="w-2.5 lg:w-3 h-2.5 lg:h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $statusMap = ['pending'=>['Menunggu','bg-amber-100 text-amber-700'],'accepted'=>['Diproses','bg-blue-100 text-blue-700'],'shipped'=>['Dikirim','bg-purple-100 text-purple-700'],'done'=>['Selesai','bg-green-100 text-green-700'],'rejected'=>['Ditolak','bg-red-100 text-red-700'],'dibatalkan'=>['Dibatalkan','bg-gray-100 text-gray-600']];
            [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status,'bg-gray-100 text-gray-600'];
        ?>
        <div class="order-row flex items-center justify-between px-4 lg:px-6 py-3 lg:py-5 border-b border-gray-50/60">
            <div class="flex-1 min-w-0">
                <p class="order-text text-xs lg:text-sm font-bold text-gray-900 truncate"><?php echo e($order->product->name ?? 'Pesanan #'.$order->id); ?></p>
                <p class="order-meta text-[10px] lg:text-xs text-gray-400 mt-1"><?php echo e($order->store->name ?? '-'); ?> · <?php echo e($order->created_at->diffForHumans()); ?></p>
            </div>
            <div class="flex items-center gap-2 lg:gap-4 ml-3 lg:ml-4 flex-shrink-0">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] lg:text-xs text-gray-400 font-medium">Total</p>
                    <p class="text-xs lg:text-sm font-black text-gray-900">Rp <?php echo e(number_format($order->total_price,0,',','.')); ?></p>
                </div>
                <span class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg lg:rounded-xl text-[9px] lg:text-xs font-black uppercase tracking-wider <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                <a href="<?php echo e(route('chat.show', $order)); ?>" class="w-7 lg:w-9 h-7 lg:h-9 rounded-lg lg:rounded-xl flex items-center justify-center text-white text-xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg" style="background:linear-gradient(135deg,#72bf77,#4db85a)">
                    <svg class="w-3 lg:w-4 h-3 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="flex flex-col items-center justify-center py-12 lg:py-20 text-center">
            <span class="text-4xl lg:text-6xl mb-3 lg:mb-5">📭</span>
            <p class="text-gray-700 font-bold text-sm lg:text-lg mb-1 lg:mb-2">Belum ada pesanan</p>
            <p class="text-xs lg:text-sm text-gray-400 mb-4 lg:mb-6">Yuk mulai belanja produk dari tetangga!</p>
            <a href="<?php echo e(route('buyer.products')); ?>" class="px-4 lg:px-6 py-2 lg:py-3 rounded-xl lg:rounded-2xl text-xs lg:text-sm font-bold text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="background:linear-gradient(135deg,#72bf77,#4db85a)">Mulai Belanja</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/buyer/dashboard.blade.php ENDPATH**/ ?>