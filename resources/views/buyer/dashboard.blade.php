@extends('layouts.dashboard')
@section('title', 'Dashboard Pembeli — Arradea')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .stat-card { background:rgba(255,255,255,0.75); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(114,191,119,0.12); transition:all .35s cubic-bezier(0.4,0,0.2,1); }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(114,191,119,.15); border-color:rgba(114,191,119,0.25); }
    .action-card { transition:all .3s cubic-bezier(0.4,0,0.2,1); }
    .action-card:hover { transform:translateY(-3px) scale(1.02); box-shadow:0 12px 28px rgba(114,191,119,.15); }
    .order-row { transition:all .25s cubic-bezier(0.4,0,0.2,1); }
    .order-row:hover { background:rgba(114,191,119,0.04); transform:translateX(4px); }
</style>
@endpush

@section('content')
@php
    $recentOrders = auth()->user()->orders()->with('store','product')->latest()->take(5)->get();
@endphp

<div class="space-y-6 fade-up">

    {{-- Greeting --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Halo, {{ explode(' ', auth()->user()->name)[0] }}! <span class="inline-block animate-bounce">👋</span></h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('buyer.products') }}" class="hidden sm:flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl active:scale-95" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 8px 24px rgba(114,191,119,.3)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Mulai Belanja
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <div class="stat-card rounded-3xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(114,191,119,.12)">📦</div>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Total</span>
            </div>
            <p class="text-4xl font-black text-gray-900 mb-1">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-500 font-medium mb-3">Total Pesanan</p>
            <a href="{{ route('buyer.orders') }}" class="text-xs font-bold inline-flex items-center gap-1 group" style="color:#72bf77">
                Lihat semua
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="stat-card rounded-3xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(245,158,11,.12)">⏳</div>
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Proses</span>
            </div>
            <p class="text-4xl font-black text-amber-500 mb-1">{{ $pendingOrders }}</p>
            <p class="text-xs text-gray-500 font-medium">Sedang Diproses</p>
        </div>

        <div class="stat-card rounded-3xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(34,197,94,.12)">✅</div>
                <span class="text-xs font-bold uppercase tracking-widest text-green-400">Selesai</span>
            </div>
            <p class="text-4xl font-black text-green-500 mb-1">{{ $completedOrders }}</p>
            <p class="text-xs text-gray-500 font-medium">Pesanan Selesai</p>
        </div>

        <div class="stat-card rounded-3xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(114,191,119,.12)">🛒</div>
                <span class="text-xs font-bold uppercase tracking-widest" style="color:#72bf77">Keranjang</span>
            </div>
            <p class="text-4xl font-black mb-1" style="color:#72bf77">{{ $cartCount }}</p>
            <p class="text-xs text-gray-500 font-medium mb-3">Item di Keranjang</p>
            <a href="{{ route('buyer.cart') }}" class="text-xs font-bold inline-flex items-center gap-1 group" style="color:#72bf77">
                Checkout
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="stat-card rounded-3xl p-6 shadow-lg">
        <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-5">Aksi Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('buyer.products') }}" class="action-card flex flex-col items-center gap-3 p-5 rounded-2xl text-center shadow-sm" style="background:rgba(240,250,241,0.6);border:1px solid rgba(114,191,119,0.15)">
                <span class="text-3xl">🛍️</span>
                <span class="text-sm font-bold text-gray-700">Belanja</span>
            </a>
            <a href="{{ route('buyer.cart') }}" class="action-card flex flex-col items-center gap-3 p-5 rounded-2xl text-center shadow-sm relative" style="background:rgba(255,247,237,0.6);border:1px solid rgba(254,215,170,0.4)">
                <span class="text-3xl">🛒</span>
                <span class="text-sm font-bold text-gray-700">Keranjang</span>
                @if($cartCount > 0)<span class="absolute top-3 right-3 px-2 py-0.5 rounded-full text-xs font-black text-white" style="background:#ea580c">{{ $cartCount }}</span>@endif
            </a>
            <a href="{{ route('buyer.orders') }}" class="action-card flex flex-col items-center gap-3 p-5 rounded-2xl text-center shadow-sm" style="background:rgba(239,246,255,0.6);border:1px solid rgba(191,219,254,0.4)">
                <span class="text-3xl">📋</span>
                <span class="text-sm font-bold text-gray-700">Pesanan</span>
            </a>
            <button @click="chatModal=true" class="action-card flex flex-col items-center gap-3 p-5 rounded-2xl text-center shadow-sm" style="background:rgba(240,253,244,0.6);border:1px solid rgba(187,247,208,0.4)">
                <span class="text-3xl">💬</span>
                <span class="text-sm font-bold text-gray-700">Chat</span>
            </button>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="stat-card rounded-3xl overflow-hidden shadow-lg">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100/60">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pesanan Terbaru</h2>
            <a href="{{ route('buyer.orders') }}" class="text-xs font-bold inline-flex items-center gap-1 group transition" style="color:#72bf77">
                Lihat Semua
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @forelse($recentOrders as $order)
        @php
            $statusMap = ['pending'=>['Menunggu','bg-amber-100 text-amber-700'],'accepted'=>['Diproses','bg-blue-100 text-blue-700'],'done'=>['Selesai','bg-green-100 text-green-700'],'rejected'=>['Ditolak','bg-red-100 text-red-700'],'dibatalkan'=>['Dibatalkan','bg-gray-100 text-gray-600']];
            [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status,'bg-gray-100 text-gray-600'];
        @endphp
        <div class="order-row flex items-center justify-between px-6 py-5 border-b border-gray-50/60">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ $order->product->name ?? 'Pesanan #'.$order->id }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $order->store->name ?? '-' }} · {{ $order->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-4 ml-4 flex-shrink-0">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-gray-400 font-medium">Total</p>
                    <p class="text-sm font-black text-gray-900">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider {{ $statusClass }}">{{ $statusLabel }}</span>
                <a href="{{ route('chat.show', $order) }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg" style="background:linear-gradient(135deg,#72bf77,#4db85a)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <span class="text-6xl mb-5">📭</span>
            <p class="text-gray-700 font-bold text-lg mb-2">Belum ada pesanan</p>
            <p class="text-sm text-gray-400 mb-6">Yuk mulai belanja produk dari tetangga!</p>
            <a href="{{ route('buyer.products') }}" class="px-6 py-3 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="background:linear-gradient(135deg,#72bf77,#4db85a)">Mulai Belanja</a>
        </div>
        @endforelse
    </div>
</div>
@endsection