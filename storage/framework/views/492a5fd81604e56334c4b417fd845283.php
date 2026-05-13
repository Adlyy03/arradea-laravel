<?php $__env->startSection('title', 'Keranjang Belanja — Arradea'); ?>
<?php $__env->startSection('page_title', 'Keranjang'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-4 fade-up">

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
                <form action="<?php echo e(route('buyer.cart.checkout')); ?>" method="POST" id="desktop-checkout-form">
                    <?php echo csrf_field(); ?>
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full sm:w-72 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 transition mb-2 resize-none"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('notes')); ?></textarea>
                    <div class="mb-3 p-3 rounded-xl border" style="background:rgba(114,191,119,.08);border-color:rgba(114,191,119,.3)">
                        <p class="text-xs font-semibold text-gray-700" style="color:#3fa348">💳 Pembayaran dilakukan saat barang diterima (COD / Cash on Delivery)</p>
                    </div>
                    <button type="button" onclick="openCODModal('desktop-checkout-form')" class="w-full sm:w-72 py-3.5 rounded-xl font-black text-base text-white transition hover:opacity-90 hover:-translate-y-0.5" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
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
                <div id="mobile-checkout-form" class="hidden mb-3">
                    <textarea name="notes" rows="2" maxlength="1000"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 transition resize-none mb-2"
                        placeholder="Catatan untuk penjual (opsional)"
                        style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('notes')); ?></textarea>
                </div>
                <div class="mb-2 p-2.5 rounded-lg border text-[11px] font-semibold" style="background:rgba(114,191,119,.08);border-color:rgba(114,191,119,.3);color:#3fa348">
                    💳 COD (Cash on Delivery) - Bayar saat barang diterima
                </div>
                <button type="button" onclick="openCODModal('mobile-checkout-form-main')" class="w-full py-3.5 rounded-xl font-black text-base text-white transition active:scale-95" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                    Pesan Sekarang →
                </button>
            </form>
        </div>
    </div>
    
    <div class="sm:hidden h-32"></div>
    <?php endif; ?>
</div>


<div id="codModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl lg:rounded-3xl max-w-md w-full shadow-2xl animate-in fade-in scale-in">
        
        <div class="px-6 lg:px-8 py-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl" style="background:rgba(114,191,119,.15)">
                    💳
                </div>
                <div>
                    <h3 class="text-lg lg:text-xl font-black text-gray-900">Konfirmasi Pembayaran</h3>
                    <p class="text-xs lg:text-sm text-gray-500 mt-0.5">Cash on Delivery (COD)</p>
                </div>
            </div>
        </div>

        
        <div class="px-6 lg:px-8 py-5 lg:py-6">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 mb-4 border border-green-200">
                <p class="text-sm lg:text-base font-black text-gray-900 mb-2">Metode Pembayaran</p>
                <p class="text-sm lg:text-base font-semibold text-gray-700 leading-relaxed">
                    Pesanan ini menggunakan metode pembayaran <span style="color:#3fa348">COD (Cash on Delivery)</span>.
                </p>
                <p class="text-xs lg:text-sm text-gray-600 mt-3 leading-relaxed">
                    ✓ Pembayaran dilakukan saat barang diterima
                </p>
                <p class="text-xs lg:text-sm text-gray-600 leading-relaxed">
                    ✓ Anda bisa mengecek barang sebelum membayar
                </p>
            </div>

            <p class="text-xs lg:text-sm text-gray-600 text-center">
                Apakah Anda ingin melanjutkan pesanan dengan metode COD ini?
            </p>
        </div>

        
        <div class="px-6 lg:px-8 py-4 lg:py-5 border-t border-gray-100 flex gap-3">
            <button type="button" onclick="closeCODModal()" class="flex-1 px-4 py-3 rounded-xl font-black text-sm lg:text-base text-gray-700 bg-gray-100 hover:bg-gray-200 transition active:scale-95">
                Batal
            </button>
            <button type="button" onclick="confirmCODOrder()" class="flex-1 px-4 py-3 rounded-xl font-black text-sm lg:text-base text-white transition hover:opacity-90 active:scale-95 shadow-lg" style="background:#72bf77;box-shadow:0 4px 12px rgba(114,191,119,.4)">
                Lanjutkan Pesan
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let pendingFormId = null;

function changeQty(cartId, delta){
    const input = document.getElementById('qty-'+cartId);
    if(!input) return;
    const min = parseInt(input.min)||1;
    const max = parseInt(input.max)||99;
    const newVal = Math.min(max, Math.max(min, parseInt(input.value)+delta));
    input.value = newVal;
    document.getElementById('cart-qty-'+cartId).submit();
}

function openCODModal(formId){
    pendingFormId = formId;
    document.getElementById('codModal').classList.remove('hidden');
    document.getElementById('codModal').classList.add('flex');
}

function closeCODModal(){
    document.getElementById('codModal').classList.add('hidden');
    document.getElementById('codModal').classList.remove('flex');
    pendingFormId = null;
}

function confirmCODOrder(){
    if(pendingFormId){
        document.getElementById(pendingFormId).submit();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event){
    const modal = document.getElementById('codModal');
    if(event.target === modal){
        closeCODModal();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/buyer/cart/index.blade.php ENDPATH**/ ?>