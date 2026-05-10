@props(['product', 'variant' => null, 'showBadge' => true])

@php
    $pricing = $product->calculatePricing($variant);
    $hasDiscount = $pricing['discount_percent'] > 0;
@endphp

<div {{ $attributes->merge(['class' => 'product-price']) }}>
    @if($hasDiscount && $showBadge)
        <span class="inline-block px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-md mb-1">
            {{ round($pricing['discount_percent']) }}% OFF
        </span>
    @endif
    
    <div class="flex items-center gap-2">
        @if($hasDiscount)
            <span class="text-sm text-gray-400 line-through">
                Rp {{ number_format($pricing['unit_original'], 0, ',', '.') }}
            </span>
            <span class="text-lg font-bold text-green-600">
                Rp {{ number_format($pricing['unit_final'], 0, ',', '.') }}
            </span>
        @else
            <span class="text-lg font-bold text-gray-900">
                Rp {{ number_format($pricing['unit_original'], 0, ',', '.') }}
            </span>
        @endif
    </div>
    
    @if($hasDiscount)
        <p class="text-xs text-green-600 mt-0.5">
            Hemat Rp {{ number_format($pricing['unit_original'] - $pricing['unit_final'], 0, ',', '.') }}
        </p>
    @endif
</div>
