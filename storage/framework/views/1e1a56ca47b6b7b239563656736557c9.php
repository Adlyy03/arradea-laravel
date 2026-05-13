<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['mode' => 'buyer']));

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

foreach (array_filter((['mode' => 'buyer']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$isSeller = $mode === 'seller';
$bgColor = $isSeller ? 'bg-purple-100' : 'bg-blue-100';
$textColor = $isSeller ? 'text-purple-700' : 'text-blue-700';
$icon = $isSeller ? '🏪' : '🛒';
$label = $isSeller ? 'Seller' : 'Buyer';
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 {$bgColor} {$textColor}"])); ?>>
    <span class="text-base"><?php echo e($icon); ?></span>
    <span><?php echo e($label); ?></span>
</span>
<?php /**PATH C:\laragon\www\arradea-laravel\resources\views/components/mode-badge.blade.php ENDPATH**/ ?>