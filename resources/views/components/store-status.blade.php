@props(['store', 'seller' => null])

@php
    $user = $seller ?? $store->user ?? null;
    $storeStatus = $user?->store_status ?? 'closed';
    $isOpen = $storeStatus === 'open';
    $autoSchedule = $user?->auto_schedule ?? false;
    $openTime = $user?->open_time;
    $closeTime = $user?->close_time;
@endphp

<div {{ $attributes->merge(['class' => 'store-status']) }}>
    <div class="flex items-center gap-2">
        <span class="relative flex h-3 w-3">
            @if($isOpen)
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            @else
                <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
            @endif
        </span>
        
        <div class="flex flex-col">
            <span class="text-sm font-semibold {{ $isOpen ? 'text-green-600' : 'text-gray-500' }}">
                {{ $isOpen ? 'Buka' : 'Tutup' }}
            </span>
            
            @if($autoSchedule && $openTime && $closeTime)
                <span class="text-xs text-gray-500">
                    {{ substr($openTime, 0, 5) }} - {{ substr($closeTime, 0, 5) }} WIB
                </span>
            @endif
        </div>
    </div>
</div>
