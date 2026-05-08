@props(['mode' => 'buyer'])

@php
$isSeller = $mode === 'seller';
$bgColor = $isSeller ? 'bg-purple-100' : 'bg-blue-100';
$textColor = $isSeller ? 'text-purple-700' : 'text-blue-700';
$icon = $isSeller ? '🏪' : '🛒';
$label = $isSeller ? 'Seller' : 'Buyer';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 {$bgColor} {$textColor}"]) }}>
    <span class="text-base">{{ $icon }}</span>
    <span>{{ $label }}</span>
</span>
