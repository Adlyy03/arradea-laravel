@extends('layouts.dashboard')

@section('title', 'Detail Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' - Arradea')
@section('page_title', 'Detail Pesanan')

@section('content')
@php
    $statusMap = [
        'pending'    => ['Menunggu Konfirmasi', 'bg-amber-100 text-amber-700', 'border-amber-200'],
        'accepted'   => ['Sedang Diproses',     'bg-blue-100 text-blue-700',   'border-blue-200'],
        'done'       => ['Selesai',             'bg-green-100 text-green-700', 'border-green-200'],
        'rejected'   => ['Ditolak',             'bg-red-100 text-red-700',     'border-red-200'],
        'dibatalkan' => ['Dibatalkan',           'bg-gray-100 text-gray-500',   'border-gray-200'],
    ];
    [$statusLabel, $statusClass, $statusBorder] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500', 'border-gray-200'];
@endphp

<div class="max-w-2xl mx-auto space-y-5 fade-up">

    {{-- Back --}}
    <a href="{{ route('seller.orders') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-900 transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Order Masuk
    </a>

    {{-- Order Header --}}
    <div class="relative overflow-hidden rounded-3xl p-6" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full opacity-10" style="background:#72bf77;filter:blur(50px)"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-1" style="color:#72bf77">Nomor Order</p>
                <h1 class="text-2xl font-black tracking-tight">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-white/50 text-xs mt-1">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->created_at->diffForHumans() }}</p>
            </div>
            <span class="{{ $statusClass }} px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- Buyer Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">👤 Informasi Pembeli</h2>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-base font-black flex-shrink-0"
                 style="background:rgba(114,191,119,.1);color:#3fa348">
                {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="font-black text-gray-900">{{ $order->user->name ?? 'Pembeli' }}</p>
                <p class="text-sm text-gray-400">{{ $order->user->email ?? '' }}</p>
                @if($order->user->phone ?? null)
                <p class="text-sm text-gray-400">{{ $order->user->phone }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">📦 Detail Produk</h2>
        <div class="flex gap-4">
            @if($order->product?->image)
            <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                <img src="{{ $order->product->image }}" alt="{{ $order->product->name }}" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="font-black text-gray-900 text-lg leading-tight">{{ $order->product->name ?? '—' }}</p>
                @php $variantName = data_get($order->product?->getVariant($order->variant_key), 'name', null); @endphp
                @if($variantName && $variantName !== 'Default')
                <p class="text-xs text-gray-400 font-bold mt-1">Varian: {{ $variantName }}</p>
                @endif
                <div class="flex flex-wrap gap-4 mt-3">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Qty</p>
                        <p class="font-black text-gray-900">{{ $order->quantity ?? 1 }}×</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Satuan</p>
                        @if(($order->discount_percent_applied ?? 0) > 0 && $order->unit_price_original)
                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($order->unit_price_original, 0, ',', '.') }}</p>
                        @endif
                        <p class="font-black text-gray-900">Rp {{ number_format($order->total_price / ($order->quantity ?: 1), 0, ',', '.') }}</p>
                    </div>
                    @if(($order->discount_percent_applied ?? 0) > 0)
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diskon</p>
                        <p class="font-black text-orange-600">{{ $order->discount_percent_applied }}%</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
            <p class="text-sm font-black text-gray-500 uppercase tracking-widest">Total Pembayaran</p>
            <p class="text-2xl font-black" style="color:#3fa348">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Notes --}}
    @if($order->notes)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <h2 class="text-xs font-black text-amber-600 uppercase tracking-widest mb-2">📝 Catatan Pembeli</h2>
        <p class="text-sm text-amber-800 font-medium">{{ $order->notes }}</p>
    </div>
    @endif

    {{-- Actions --}}
    @if(in_array($order->status, ['pending','accepted']))
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3">
        <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest">⚙️ Tindakan</h2>

        @if($order->status === 'pending')
        <div class="flex gap-3">
            <form action="/web/order/{{ $order->id }}/status" method="POST" class="flex-1">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="accepted">
                <button type="submit"
                        class="w-full h-12 rounded-2xl font-black text-sm text-white transition hover:opacity-90 active:scale-95 flex items-center justify-center gap-2 shadow-lg"
                        style="background:#72bf77;box-shadow:0 8px 24px rgba(114,191,119,.3)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Terima &amp; Proses Order
                </button>
            </form>
            <form action="/web/order/{{ $order->id }}/status" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="h-12 px-5 rounded-2xl font-black text-sm text-red-600 bg-red-100 hover:bg-red-200 transition active:scale-95">
                    Tolak
                </button>
            </form>
        </div>
        @elseif($order->status === 'accepted')
        <form action="/web/order/{{ $order->id }}/status" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="done">
            <button type="submit"
                    class="w-full h-12 rounded-2xl font-black text-sm text-white bg-blue-600 hover:bg-blue-700 transition active:scale-95 flex items-center justify-center gap-2 shadow-lg shadow-blue-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Tandai Pesanan Selesai
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- Chat --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-1">💬 Komunikasi</h2>
                <p class="text-sm text-gray-400">Chat langsung dengan pembeli terkait order ini.</p>
            </div>
            <a href="{{ route('chat.show', $order) }}"
               class="flex-shrink-0 px-5 py-2.5 rounded-xl font-black text-sm text-white transition hover:opacity-90 active:scale-95"
               style="background:#72bf77">
                Buka Chat →
            </a>
        </div>
    </div>

</div>
@endsection
