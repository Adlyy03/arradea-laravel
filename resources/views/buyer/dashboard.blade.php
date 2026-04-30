@extends('layouts.dashboard')
@section('title', 'Dashboard Pembeli — Arradea')
@section('page_title', 'Dashboard')

@section('content')
@php
    $recentOrders = auth()->user()->orders()->with('store','product')->latest()->take(5)->get();
@endphp

<div class="space-y-5 fade-up">

    {{-- Greeting --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('buyer.products') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#72bf77">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Mulai Belanja
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:rgba(114,191,119,.12)">📦</div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</span>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Total Pesanan</p>
            <a href="{{ route('buyer.orders') }}" class="text-[11px] font-bold mt-2 inline-block" style="color:#72bf77">Lihat semua →</a>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:rgba(245,158,11,.1)">⏳</div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Proses</span>
            </div>
            <p class="text-3xl font-black text-amber-500">{{ $pendingOrders }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Sedang Diproses</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:rgba(34,197,94,.1)">✅</div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-green-400">Selesai</span>
            </div>
            <p class="text-3xl font-black text-green-500">{{ $completedOrders }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Pesanan Selesai</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl" style="background:rgba(114,191,119,.12)">🛒</div>
                <span class="text-[10px] font-bold uppercase tracking-widest" style="color:#72bf77">Keranjang</span>
            </div>
            <p class="text-3xl font-black" style="color:#72bf77">{{ $cartCount }}</p>
            <p class="text-xs text-gray-400 mt-1 font-medium">Item di Keranjang</p>
            <a href="{{ route('buyer.cart') }}" class="text-[11px] font-bold mt-2 inline-block" style="color:#72bf77">Checkout →</a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('buyer.products') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl hover:shadow-md transition-all text-center group" style="background:#f0faf1;border:1px solid #d8f3da">
                <span class="text-2xl group-hover:scale-110 transition">🛍️</span>
                <span class="text-xs font-bold text-gray-700">Belanja</span>
            </a>
            <a href="{{ route('buyer.cart') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl hover:shadow-md transition-all text-center group" style="background:#fff7ed;border:1px solid #fed7aa">
                <span class="text-2xl group-hover:scale-110 transition">🛒</span>
                <span class="text-xs font-bold text-gray-700">Keranjang</span>
                @if($cartCount > 0)<span class="text-[10px] font-black" style="color:#ea580c">{{ $cartCount }} item</span>@endif
            </a>
            <a href="{{ route('buyer.orders') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl hover:shadow-md transition-all text-center group" style="background:#eff6ff;border:1px solid #bfdbfe">
                <span class="text-2xl group-hover:scale-110 transition">📋</span>
                <span class="text-xs font-bold text-gray-700">Pesanan</span>
            </a>
            <button @click="chatModal=true" class="flex flex-col items-center gap-2 p-4 rounded-2xl hover:shadow-md transition-all text-center group" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <span class="text-2xl group-hover:scale-110 transition">💬</span>
                <span class="text-xs font-bold text-gray-700">Chat</span>
            </button>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pesanan Terbaru</h2>
            <a href="{{ route('buyer.orders') }}" class="text-xs font-bold transition" style="color:#72bf77">Lihat Semua →</a>
        </div>

        @forelse($recentOrders as $order)
        @php
            $statusMap = ['pending'=>['Menunggu','bg-amber-100 text-amber-700'],'accepted'=>['Diproses','bg-blue-100 text-blue-700'],'done'=>['Selesai','bg-green-100 text-green-700'],'rejected'=>['Ditolak','bg-red-100 text-red-700'],'dibatalkan'=>['Dibatalkan','bg-gray-100 text-gray-600']];
            [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status,'bg-gray-100 text-gray-600'];
        @endphp
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50/50 hover:bg-gray-50/50 transition">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ $order->product->name ?? 'Pesanan #'.$order->id }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $order->store->name ?? '-' }} · {{ $order->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-sm font-black text-gray-900">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">{{ $statusLabel }}</span>
                <a href="{{ route('chat.show', $order) }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs transition hover:opacity-80" style="background:#72bf77">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <span class="text-5xl mb-4">📭</span>
            <p class="text-gray-700 font-bold mb-1">Belum ada pesanan</p>
            <p class="text-sm text-gray-400 mb-5">Yuk mulai belanja produk dari tetangga!</p>
            <a href="{{ route('buyer.products') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#72bf77">Mulai Belanja</a>
        </div>
        @endforelse
    </div>
</div>
@endsection