<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$activeMode = $user->getActiveMode();
$canSwitchToSeller = $user->canSwitchToSellerMode();
?>

<div class="flex flex-col gap-2">

    
    <form method="POST" action="<?php echo e(route('mode.switch')); ?>" class="m-0">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="mode" value="buyer">
        <button type="submit"
            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl border-2 transition-all duration-200 cursor-pointer hover:shadow-sm text-left"
            style="border-color:<?php echo e($activeMode==='buyer' ? '#3b82f6' : '#f0f0f0'); ?>;
                   background:<?php echo e($activeMode==='buyer' ? '#eff6ff' : '#fafafa'); ?>;">

            
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                style="background:<?php echo e($activeMode==='buyer' ? '#3b82f6' : '#e5e7eb'); ?>;">🛒</div>

            
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 leading-tight">Mode Buyer</span>
                    <?php if($activeMode==='buyer'): ?>
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded-full font-black text-white flex-shrink-0"
                            style="background:#3b82f6; line-height:1.4;">AKTIF</span>
                    <?php endif; ?>
                </div>
                <div class="text-xs text-gray-400 mt-0.5 leading-tight">Cari & beli produk favoritmu</div>
            </div>

            
            <?php if($activeMode==='buyer'): ?>
                <svg class="w-5 h-5 flex-shrink-0" style="color:#3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            <?php else: ?>
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            <?php endif; ?>
        </button>
    </form>

    
    <?php if($canSwitchToSeller): ?>
        <form method="POST" action="<?php echo e(route('mode.switch')); ?>" class="m-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="mode" value="seller">
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-3 rounded-xl border-2 transition-all duration-200 cursor-pointer hover:shadow-sm text-left"
                style="border-color:<?php echo e($activeMode==='seller' ? '#f59e0b' : '#f0f0f0'); ?>;
                       background:<?php echo e($activeMode==='seller' ? '#fffbeb' : '#fafafa'); ?>;">

                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                    style="background:<?php echo e($activeMode==='seller' ? '#f59e0b' : '#e5e7eb'); ?>;">🏪</div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900 leading-tight">Mode Seller</span>
                        <?php if($activeMode==='seller'): ?>
                            <span class="inline-block text-[9px] px-1.5 py-0.5 rounded-full font-black text-white flex-shrink-0"
                                style="background:#f59e0b; line-height:1.4;">AKTIF</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5 leading-tight">Kelola toko & mulai jualan</div>
                </div>

                <?php if($activeMode==='seller'): ?>
                    <svg class="w-5 h-5 flex-shrink-0" style="color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                <?php endif; ?>
            </button>
        </form>
    <?php else: ?>
        
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 opacity-60">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl bg-gray-200 grayscale">🏪</div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-gray-400 leading-tight">Mode Seller</div>
                <div class="text-xs text-gray-400 mt-0.5 leading-tight">
                    <?php if($user->is_seller): ?> Menunggu verifikasi admin <?php else: ?> Daftar seller dahulu <?php endif; ?>
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
        </div>
    <?php endif; ?>

</div>
<?php /**PATH C:\laragon\www\arradea-laravel\resources\views/components/bottom-sheet-switcher.blade.php ENDPATH**/ ?>