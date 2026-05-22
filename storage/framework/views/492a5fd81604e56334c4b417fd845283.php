<?php $__env->startSection('title', 'Keranjang Belanja — Arradea'); ?>
<?php $__env->startSection('page_title', 'Keranjang'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-4 fade-up">
    <?php
        $singleStoreIds = $carts->pluck('product.store_id')->unique();
        $singleStore = $singleStoreIds->count() === 1 ? $carts->first()?->product?->store : null;
        $singleSeller = $singleStore?->user;
        $hasQrisSeller = $singleSeller && $singleSeller->hasQrisPaymentSetup();
        
        // Debug info
        $debugInfo = [
            'has_cart' => $carts->isNotEmpty(),
            'single_store' => $singleStoreIds->count() === 1,
            'store_id' => $singleStore?->id,
            'seller_id' => $singleSeller?->id,
            'seller_name' => $singleSeller?->name,
            'has_qris_image' => filled($singleSeller?->qris_image ?? null),
            'has_payment_name' => filled($singleSeller?->payment_name ?? null),
            'qris_image_path' => $singleSeller?->qris_image,
            'payment_name' => $singleSeller?->payment_name,
            'hasQrisSeller' => $hasQrisSeller,
        ];
    ?>

    <!-- 
    <?php if(config('app.debug')): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <p class="font-black text-blue-900 text-xs mb-2">🔍 DEBUG INFO:</p>
        <pre class="text-[10px] text-blue-700 overflow-auto"><?php echo e(json_encode($debugInfo, JSON_PRETTY_PRINT)); ?></pre>
    </div>
    <?php endif; ?> -->

    
    <?php if($errors->any()): ?>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl">❌</span>
            <div class="flex-1">
                <p class="font-black text-red-900 text-sm mb-1">Terjadi Kesalahan</p>
                <ul class="text-xs text-red-700 space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>• <?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-green-900 text-sm"><?php echo e(session('success')); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-900">🛒 Keranjang <span style="color:#72bf77">Belanja</span></h1>
        <?php if($carts->isNotEmpty()): ?>
            <span class="px-3 py-1 rounded-xl text-xs font-bold" style="background:rgba(114,191,119,.12);color:#3fa348"><?php echo e($carts->count()); ?> item</span>
        <?php endif; ?>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $isDiscount = ($cart->pricing['discount_percent'] ?? 0) > 0;
        $unitPrice = $isDiscount ? $cart->pricing['unit_final'] : $cart->pricing['unit_original'];
        $totalItem = $isDiscount ? $cart->pricing['total_final'] : $cart->pricing['total_original'];
    ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 flex flex-col sm:flex-row gap-4 hover:shadow-md transition">
        <img src="<?php echo e($cart->product->image ?? 'https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'); ?>"
            alt="<?php echo e($cart->product->name); ?>"
            class="w-24 h-24 rounded-xl object-cover flex-shrink-0 self-center sm:self-start"
            onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:#72bf77">🏪 <?php echo e($cart->product->store->name ?? 'Arradea'); ?></p>
            <h3 class="font-black text-gray-900 leading-tight mb-1"><?php echo e($cart->product->name); ?></h3>
            <?php if($cart->variant_key): ?>
                <p class="text-xs text-gray-400 mb-1.5">Varian: <?php echo e(data_get($cart->product->getVariant($cart->variant_key), 'name', $cart->variant_key)); ?></p>
            <?php endif; ?>
            <div class="flex items-center gap-2">
                <?php if($isDiscount): ?>
                    <span class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($cart->pricing['unit_original'],0,',','.')); ?></span>
                    <span class="font-black text-base" style="color:#72bf77">Rp <?php echo e(number_format($unitPrice,0,',','.')); ?></span>
                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black text-white" style="background:#72bf77">-<?php echo e($cart->pricing['discount_percent']); ?>%</span>
                <?php else: ?>
                    <span class="font-black text-base text-gray-900">Rp <?php echo e(number_format($unitPrice,0,',','.')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3 flex-shrink-0">
            <div class="flex items-center gap-1.5 bg-gray-100 rounded-xl px-1">
                <form action="<?php echo e(route('buyer.cart.update', $cart)); ?>" method="POST" id="cart-qty-<?php echo e($cart->id); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="changeQty(<?php echo e($cart->id); ?>, -1)" class="w-7 h-7 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition text-sm font-black">−</button>
                        <input type="number" name="quantity" id="qty-<?php echo e($cart->id); ?>" value="<?php echo e($cart->quantity); ?>" min="1" max="<?php echo e($cart->product->stock); ?>"
                            class="w-10 bg-transparent border-none text-center font-black text-sm text-gray-900 focus:outline-none">
                        <button type="button" onclick="changeQty(<?php echo e($cart->id); ?>, 1)" class="w-7 h-7 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition text-sm font-black">+</button>
                    </div>
                </form>
            </div>
            <div class="text-right">
                <?php if($isDiscount): ?>
                    <p class="text-[10px] text-gray-400 line-through">Rp <?php echo e(number_format($cart->pricing['total_original'],0,',','.')); ?></p>
                <?php endif; ?>
                <p class="font-black text-gray-900">Rp <?php echo e(number_format($totalItem,0,',','.')); ?></p>
            </div>
            <form action="<?php echo e(route('buyer.cart.destroy', $cart)); ?>" method="POST" onsubmit="return confirmSubmit(event, <?php echo \Illuminate\Support\Js::from('Hapus '.$cart->product->name.' dari keranjang?')->toHtml() ?>)">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-2xl border border-gray-100 flex flex-col items-center justify-center py-20 text-center">
        <span class="text-5xl mb-4">🛒</span>
        <h3 class="text-xl font-black text-gray-900 mb-2">Keranjang masih kosong</h3>
        <p class="text-sm text-gray-400 mb-6">Belum ada produk yang kamu tambahkan.</p>
        <a href="<?php echo e(route('buyer.products')); ?>" class="px-6 py-3 rounded-2xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">Mulai Belanja</a>
    </div>
    <?php endif; ?>

    <?php if($carts->isNotEmpty()): ?>
    
    <div class="hidden sm:block bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-0.5">
                <?php if(($totalOriginal ?? 0) > ($totalFinal ?? 0)): ?>
                    <p class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($totalOriginal,0,',','.')); ?></p>
                    <p class="text-xs font-bold" style="color:#72bf77">Hemat Rp <?php echo e(number_format($totalOriginal - $totalFinal,0,',','.')); ?></p>
                <?php endif; ?>
                <p class="text-sm text-gray-500 font-medium">Total Pembayaran</p>
                <p class="text-3xl font-black text-gray-900">Rp <?php echo e(number_format($totalFinal ?? 0,0,',','.')); ?></p>
            </div>
            <div class="w-full sm:w-auto space-y-3">
                <form action="<?php echo e(route('buyer.cart.checkout')); ?>" method="POST" id="checkout-form-desktop">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="payment_method" id="payment-method-desktop" value="cod">
                    <input type="hidden" name="payment_proof_base64" id="payment-proof-base64-desktop">
                    
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button type="button" onclick="selectPaymentMethod('desktop', 'cod')" id="btn-cod-desktop" class="rounded-xl border-2 border-primary-500 bg-primary-50 p-3 text-left transition">
                            <span class="block text-xs font-black text-gray-900">COD</span>
                            <span class="block text-[10px] text-gray-500 mt-1">Bayar di tempat</span>
                        </button>
                        <a href="<?php echo e($hasQrisSeller ? route('buyer.cart.qris') : '#'); ?>" id="btn-qris-desktop" class="rounded-xl border-2 p-3 text-left transition <?php echo e($hasQrisSeller ? 'border-gray-200 bg-white hover:border-primary-300' : 'border-gray-200 bg-gray-100 cursor-not-allowed'); ?>" <?php echo e($hasQrisSeller ? '' : 'onclick="return false;"'); ?>>
                            <span class="block text-xs font-black <?php echo e($hasQrisSeller ? 'text-gray-900' : 'text-gray-400'); ?>">QRIS Manual</span>
                            <span class="block text-[10px] <?php echo e($hasQrisSeller ? 'text-gray-500' : 'text-gray-400'); ?> mt-1">
                                <?php if($hasQrisSeller): ?>
                                    Scan & bayar
                                <?php else: ?>
                                    Seller belum setup
                                <?php endif; ?>
                            </span>
                        </a>
                    </div>
                    
                    <?php if(!$hasQrisSeller && $singleStore): ?>
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs font-bold text-amber-900">⚠️ QRIS Belum Tersedia</p>
                        <p class="text-[10px] text-amber-700 mt-1">
                            Seller <strong><?php echo e($singleStore->name); ?></strong> belum mengaktifkan pembayaran QRIS. 
                            <?php if($singleSeller): ?>
                                <?php if(!$singleSeller->qris_image): ?>
                                    Seller perlu upload gambar QRIS
                                <?php endif; ?>
                                <?php if(!$singleSeller->payment_name): ?>
                                    <?php echo e(!$singleSeller->qris_image ? ' dan' : 'Seller perlu'); ?> mengisi nama penerima
                                <?php endif; ?>
                                di halaman pengaturan toko.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full sm:w-72 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 transition mb-2 resize-none"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('notes')); ?></textarea>
                    <button type="submit" class="w-full sm:w-72 py-3.5 rounded-xl font-black text-base text-white transition hover:opacity-90 hover:-translate-y-0.5" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                        Pesan Sekarang →
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-2xl" style="padding-bottom:env(safe-area-inset-bottom)">
        <div class="px-5 py-3">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <?php if(($totalOriginal ?? 0) > ($totalFinal ?? 0)): ?>
                        <p class="text-[10px] text-gray-400 line-through">Rp <?php echo e(number_format($totalOriginal,0,',','.')); ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500 font-medium">Total</p>
                    <p class="text-xl font-black text-gray-900">Rp <?php echo e(number_format($totalFinal ?? 0,0,',','.')); ?></p>
                </div>
                <button type="button" onclick="document.getElementById('mobile-checkout-form').classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-gray-100">
                    + Catatan
                </button>
            </div>
            <form id="mobile-checkout-form-main" action="<?php echo e(route('buyer.cart.checkout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="payment_method" id="payment-method-mobile" value="cod">
                <input type="hidden" name="payment_proof_base64" id="payment-proof-base64-mobile">
                
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <button type="button" onclick="selectPaymentMethod('mobile', 'cod')" id="btn-cod-mobile" class="rounded-xl border border-primary-500 bg-primary-50 p-2.5 text-left transition">
                        <span class="block text-[10px] font-black text-gray-900">COD</span>
                        <span class="block text-[9px] text-gray-500 mt-0.5">Bayar di tempat</span>
                    </button>
                    <a href="<?php echo e($hasQrisSeller ? route('buyer.cart.qris') : '#'); ?>" id="btn-qris-mobile" class="rounded-xl border p-2.5 text-left transition <?php echo e($hasQrisSeller ? 'border-gray-200 bg-gray-50 hover:border-primary-300' : 'border-gray-200 bg-gray-100 cursor-not-allowed'); ?>" <?php echo e($hasQrisSeller ? '' : 'onclick="return false;"'); ?>>
                        <span class="block text-[10px] font-black <?php echo e($hasQrisSeller ? 'text-gray-900' : 'text-gray-400'); ?>">QRIS</span>
                        <span class="block text-[9px] <?php echo e($hasQrisSeller ? 'text-gray-500' : 'text-gray-400'); ?> mt-0.5">
                            <?php if($hasQrisSeller): ?>
                                Scan & bayar
                            <?php else: ?>
                                Belum tersedia
                            <?php endif; ?>
                        </span>
                    </a>
                </div>
                
                <?php if(!$hasQrisSeller && $singleStore): ?>
                <div class="mb-3 p-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-[10px] font-bold text-amber-900">⚠️ QRIS belum tersedia</p>
                    <p class="text-[9px] text-amber-700 mt-0.5">
                        <?php if($singleSeller): ?>
                            <?php if(!$singleSeller->qris_image && !$singleSeller->payment_name): ?>
                                Seller belum upload QRIS & nama penerima.
                            <?php elseif(!$singleSeller->qris_image): ?>
                                Seller belum upload gambar QRIS.
                            <?php elseif(!$singleSeller->payment_name): ?>
                                Seller belum isi nama penerima.
                            <?php endif; ?>
                        <?php else: ?>
                            Seller belum setup QRIS.
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <div id="mobile-checkout-form" class="hidden mb-3">
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 transition resize-none"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('notes')); ?></textarea>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl font-black text-base text-white transition active:scale-95" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                    Pesan Sekarang →
                </button>
            </form>
        </div>
    </div>
    
    <div class="sm:hidden h-32"></div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function selectPaymentMethod(scope, method) {
    const methodInput = document.getElementById('payment-method-' + scope);
    const codBtn = document.getElementById('btn-cod-' + scope);
    
    if (methodInput) {
        methodInput.value = method;
    }
}

function changeQty(cartId, delta){
    const input = document.getElementById('qty-'+cartId);
    if(!input) return;
    const min = parseInt(input.min)||1;
    const max = parseInt(input.max)||99;
    const newVal = Math.min(max, Math.max(min, parseInt(input.value)+delta));
    input.value = newVal;
    document.getElementById('cart-qty-'+cartId).submit();
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/buyer/cart/index.blade.php ENDPATH**/ ?>