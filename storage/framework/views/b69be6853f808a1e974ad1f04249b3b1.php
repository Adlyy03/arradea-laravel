<?php $__env->startSection('title', 'Temukan Produk — Arradea'); ?>
<?php $__env->startSection('page_title', 'Semua Produk'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Force 3 columns on mobile */
    .product-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    
    @media(min-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        }
    }
    
    /* Mobile product grid - 3 columns compact */
    @media(max-width:1023px){
        .product-grid { gap: 8px !important; }
        .product-card { border-radius: 10px !important; }
        .product-image-wrapper { 
            aspect-ratio: 1 / 1 !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        .product-image { 
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
        }
        .product-content { padding: 7px !important; }
        .product-category { display: none !important; }
        .product-store { font-size: 9px !important; margin-bottom: 3px !important; }
        .product-name { font-size: 11px !important; line-height: 1.3 !important; margin-bottom: 4px !important; -webkit-line-clamp: 2 !important; }
        .product-price { font-size: 12px !important; font-weight: 800 !important; }
        .product-old-price { font-size: 9px !important; }
        .product-stock { display: none !important; }
        .product-btn { padding: 7px 10px !important; font-size: 10px !important; border-radius: 7px !important; }
        .discount-badge { top: 4px !important; left: 4px !important; padding: 4px 7px !important; font-size: 9px !important; border-radius: 6px !important; font-weight: 900 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.15) !important; }
        .search-bar { padding: 12px !important; border-radius: 14px !important; }
        .search-bar input { height: 38px !important; font-size: 13px !important; border-radius: 11px !important; padding-left: 12px !important; }
        .search-bar button { height: 38px !important; padding: 0 14px !important; font-size: 12px !important; border-radius: 11px !important; }
        .category-pills { gap: 6px !important; padding-bottom: 5px !important; }
        .category-pill { padding: 12px 12px !important; font-size: 11px !important; border-radius: 8px !important; white-space: nowrap !important; line-height: 1.3 !important; }
        .results-header { font-size: 12px !important; }
    }
    
    @media(max-width:375px){
        .product-name { font-size: 10px !important; }
        .product-price { font-size: 11px !important; }
        .product-old-price { font-size: 8px !important; }
        .product-btn { font-size: 9px !important; padding: 6px 8px !important; }
        .discount-badge { font-size: 8px !important; padding: 3px 5px !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div data-aos="fade-up" class="space-y-5">

    
    <div data-aos="fade-down" data-aos-delay="100" class="search-bar bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4">
        <form action="<?php echo e(route('buyer.products')); ?>" method="GET">
            <?php if(request('category')): ?>
                <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
            <?php endif; ?>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input id="product-search" type="search" name="q" value="<?php echo e($keyword ?? request('q')); ?>"
                        placeholder="Cari produk..."
                        class="w-full h-9 lg:h-10 bg-gray-50 border border-gray-200 rounded-lg lg:rounded-xl pl-3 lg:pl-4 pr-3 lg:pr-4 text-xs lg:text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                </div>
                <button type="submit" class="h-9 lg:h-10 px-4 lg:px-5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">Cari</button>
            </div>
        </form>
    </div>

    
    <div data-aos="fade-right" data-aos-delay="150" class="category-pills flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="<?php echo e(route('buyer.products', array_filter(['q'=>request('q')]))); ?>"
            class="category-pill flex-shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold transition <?php echo e(!request('category') ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>"
            style="<?php echo e(!request('category') ? 'background:#72bf77' : ''); ?>">Semua</a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('buyer.products', array_filter(['category'=>$cat->slug,'q'=>request('q')]))); ?>"
            class="category-pill flex-shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold transition <?php echo e(request('category')===$cat->slug ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>"
            style="<?php echo e(request('category')===$cat->slug ? 'background:#72bf77' : ''); ?>"><?php echo e($cat->name); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="results-header flex items-center justify-between">
        <p class="text-xs lg:text-sm text-gray-500">
            <span class="font-bold text-gray-900"><?php echo e($products->total()); ?></span> produk
            <?php if(request('q')): ?><span> untuk "<em class="text-gray-700"><?php echo e(request('q')); ?></em>"</span><?php endif; ?>
        </p>
    </div>

    
    <div class="product-grid grid grid-cols-3 lg:grid-cols-6 gap-2 lg:gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            // Gunakan method getActiveDiscountPercent untuk mendapatkan diskon yang aktif
            $activeDiscount = $product->getActiveDiscountPercent();
            $isDiscount = $activeDiscount > 0;
            $finalPrice = $isDiscount ? round($product->price * (1 - $activeDiscount/100)) : $product->price;
        ?>
        <div data-aos="fade-up" data-aos-delay="<?php echo e(($loop->index % 6) * 50); ?>" class="product-card group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-green-100/40 hover:border-green-200/50 transition-all duration-300" data-product-id="<?php echo e($product->id); ?>">
            <div class="product-image-wrapper relative w-full aspect-square overflow-hidden bg-gray-50">
                <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk'); ?>"
                    alt="<?php echo e($product->name); ?>"
                    class="product-image w-full h-full object-cover object-center block transition-transform duration-500 group-hover:scale-105"
                    onerror="this.src='https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk'">
                <?php if($isDiscount): ?>
                    <span class="discount-badge absolute top-2 left-2 px-2 py-1 rounded-lg text-[10px] font-black text-white shadow-md" style="background:#ef4444">-<?php echo e(number_format($activeDiscount, 0)); ?>%</span>
                <?php endif; ?>
                <?php if($product->stock === 0): ?>
                    <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                        <span class="px-2 py-1 bg-gray-800 text-white text-[9px] font-black rounded-lg">Habis</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-content p-2 lg:p-3">
                <p class="product-category text-[8px] font-black uppercase tracking-wider text-gray-400 mb-0.5 hidden lg:block"><?php echo e($product->category->name ?? 'Umum'); ?></p>
                <p class="product-store text-[8px] lg:text-[10px] font-bold truncate mb-1" style="color:#72bf77">🏪 <?php echo e($product->store->name ?? 'Arradea'); ?></p>
                <h3 class="product-name font-black text-gray-900 line-clamp-2 text-[10px] lg:text-xs leading-snug mb-1.5"><?php echo e($product->name); ?></h3>
                <div class="mb-1.5 lg:mb-2">
                    <?php if($isDiscount): ?>
                        <div class="space-y-0.5">
                            <p class="product-old-price text-[9px] lg:text-[10px] text-gray-400 line-through">Rp <?php echo e(number_format($product->price,0,',','.')); ?></p>
                            <div class="flex items-center justify-between gap-1">
                                <p class="product-price font-black text-[12px] lg:text-sm leading-none" style="color:#72bf77">Rp <?php echo e(number_format($finalPrice,0,',','.')); ?></p>
                                <span class="product-stock text-[8px] lg:text-[9px] font-bold <?php echo e($product->stock > 5 ? 'text-green-600' : 'text-amber-500'); ?> hidden lg:block">Stok <?php echo e($product->stock); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center justify-between gap-1">
                            <p class="product-price font-black text-[12px] lg:text-sm leading-none text-gray-900">Rp <?php echo e(number_format($product->price,0,',','.')); ?></p>
                            <span class="product-stock text-[8px] lg:text-[9px] font-bold <?php echo e($product->stock > 5 ? 'text-green-600' : 'text-amber-500'); ?> hidden lg:block">Stok <?php echo e($product->stock); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('buyer.products.show', $product->id)); ?>"
                    class="product-btn block w-full py-1.5 lg:py-2 rounded-lg text-[9px] lg:text-[10px] font-black text-white text-center transition hover:opacity-90 <?php echo e($product->stock === 0 ? 'opacity-50 pointer-events-none' : ''); ?>"
                    style="background:#72bf77">
                    <?php echo e($product->stock > 0 ? 'Beli' : 'Habis'); ?>

                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full flex flex-col items-center justify-center py-12 lg:py-20 text-center">
            <span class="text-3xl lg:text-5xl mb-3 lg:mb-4">🔍</span>
            <p class="text-base lg:text-xl font-black text-gray-900 mb-1 lg:mb-2">Produk tidak ditemukan</p>
            <p class="text-xs lg:text-sm text-gray-400 mb-3 lg:mb-5">Coba kata kunci lain</p>
            <a href="<?php echo e(route('buyer.products')); ?>" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-white" style="background:#72bf77">Reset</a>
        </div>
        <?php endif; ?>
    </div>

    <?php if($products->hasPages()): ?>
    <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4"><?php echo e($products->links()); ?></div>
    <?php endif; ?>
</div>


<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function(){
    const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const broadcastKey = <?php echo json_encode(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')), 512) ?>;
    const broadcastCluster = <?php echo json_encode(env('PUSHER_APP_CLUSTER', 'mt1'), 512) ?>;
    const broadcastHost = <?php echo json_encode(env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1'))) ?>;
    const broadcastPort = Number(<?php echo json_encode((int) env('REVERB_PORT', env('PUSHER_PORT', 8080))) ?>);
    const broadcastScheme = <?php echo json_encode(env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http'))) ?>;
    const formatRupiah = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v||0));
    const updateCard = p => {
        if(!p||!p.id) return;
        const card = document.querySelector('[data-product-id="'+p.id+'"]');
        if(!card) return;
        
        // Hitung harga final dengan diskon
        const discount = Number(p.active_discount_percent || p.discount_percent || 0);
        const originalPrice = Number(p.price || 0);
        const finalPrice = discount > 0 ? Math.round(originalPrice * (1 - discount/100)) : originalPrice;
        
        // Update harga
        const priceEl = card.querySelector('.product-price');
        if(priceEl) {
            priceEl.textContent = formatRupiah(finalPrice);
        }
        
        // Update harga asli jika ada diskon
        const oldPriceEl = card.querySelector('.product-old-price');
        if(oldPriceEl && discount > 0) {
            oldPriceEl.textContent = formatRupiah(originalPrice);
        }
        
        // Update badge diskon
        const badgeEl = card.querySelector('.discount-badge');
        if(discount > 0 && !badgeEl) {
            // Tambah badge jika belum ada
            const imgWrapper = card.querySelector('.product-image-wrapper');
            if(imgWrapper) {
                const badge = document.createElement('span');
                badge.className = 'discount-badge absolute top-2 left-2 px-2 py-1 rounded-lg text-[10px] font-black text-white shadow-md';
                badge.style.background = '#ef4444';
                badge.textContent = '-' + Math.round(discount) + '%';
                imgWrapper.appendChild(badge);
            }
        } else if(badgeEl) {
            badgeEl.textContent = '-' + Math.round(discount) + '%';
        }
        
        // Update stok
        const s = Number(p.stock||0);
        const stockEl = card.querySelector('.product-stock');
        if(stockEl){ 
            stockEl.textContent='Stok '+s; 
            stockEl.className='text-[10px] font-bold product-stock '+(s>5?'text-green-600':'text-amber-500'); 
        }
    };
    let lastSyncAt = null;
    const poll = async () => {
        const ids = Array.from(document.querySelectorAll('[data-product-id]')).map(c=>Number(c.dataset.productId)).filter(n=>Number.isInteger(n));
        if(!ids.length) return;
        const p = new URLSearchParams(); ids.forEach(id=>p.append('ids[]',id)); if(lastSyncAt) p.append('since',lastSyncAt);
        const r = await fetch('/api/products/updates?'+p.toString(),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrfToken},credentials:'same-origin'}).catch(()=>null);
        if(!r?.ok) return;
        const d = await r.json();
        if(d.success && Array.isArray(d.data)){ d.data.forEach(updateCard); lastSyncAt = new Date().toISOString(); }
    };
    if(broadcastKey && typeof window.Echo!=='undefined'){
        new window.Echo({broadcaster:'pusher',key:broadcastKey,cluster:broadcastCluster,wsHost:broadcastHost,wsPort:broadcastPort,wssPort:broadcastPort,forceTLS:broadcastScheme==='https',enabledTransports:['ws','wss']}).channel('products').listen('.ProductUpdated',updateCard);
    }
    poll(); setInterval(()=>poll().catch(()=>{}),5000);
    const si = document.getElementById('product-search');
    if(si?.form){ let t; si.addEventListener('input',()=>{ clearTimeout(t); t=setTimeout(()=>si.form.submit(),450); }); }
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradeaaaa\resources\views/buyer/products/index.blade.php ENDPATH**/ ?>