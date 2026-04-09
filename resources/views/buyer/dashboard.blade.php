@extends('layouts.dashboard')

@section('title', 'Dashboard Pembeli - Arradea')
@section('page_title', 'Dashboard Pembeli')

@section('content')
<div class="space-y-8 lg:space-y-12">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Orders -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Pesanan</p>
                    <h3 class="text-4xl font-black text-gray-900">{{ $totalOrders }}</h3>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center text-3xl">📦</div>
            </div>
            <a href="{{ route('buyer.orders') }}" class="mt-4 text-[10px] font-bold text-primary-600 hover:text-primary-700">Lihat Semua →</a>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Pesanan Proses</p>
                    <h3 class="text-4xl font-black text-amber-600">{{ $pendingOrders }}</h3>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center text-3xl">⏳</div>
            </div>
            <p class="mt-4 text-[10px] font-bold text-amber-600">Sedang diproses seller</p>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Pesanan Selesai</p>
                    <h3 class="text-4xl font-black text-green-600">{{ $completedOrders }}</h3>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-green-100 flex items-center justify-center text-3xl">✓</div>
            </div>
            <p class="mt-4 text-[10px] font-bold text-green-600">Sudah diterima</p>
        </div>

        <!-- Cart Items -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Keranjang</p>
                    <h3 class="text-4xl font-black text-primary-600">{{ $cartCount }}</h3>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center text-3xl">🛒</div>
            </div>
            <a href="{{ route('buyer.cart') }}" class="mt-4 text-[10px] font-bold text-primary-600 hover:text-primary-700">Checkout →</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-3xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100">
        <h2 class="text-xl lg:text-2xl font-black text-gray-900 mb-6 lg:mb-8">Aksi Cepat</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <a href="{{ route('buyer.products') }}" class="flex flex-col items-center justify-center p-4 lg:p-8 rounded-2xl lg:rounded-3xl bg-primary-50 hover:bg-primary-100 transition">
                <div class="text-3xl lg:text-5xl mb-2 lg:mb-4">🛍️</div>
                <p class="font-black text-center">Belanja Produk</p>
            </a>
            <a href="{{ route('buyer.cart') }}" class="flex flex-col items-center justify-center p-4 lg:p-8 rounded-2xl lg:rounded-3xl bg-accent/10 hover:bg-accent/20 transition relative">
                <div class="text-3xl lg:text-5xl mb-2 lg:mb-4">🛒</div>
                <p class="font-black text-center">Lihat Keranjang</p>
                @if($cartCount > 0)
                    <span class="mt-3 text-xs font-black text-accent">{{ $cartCount }} item</span>
                @endif
            </a>
            <a href="{{ route('buyer.orders') }}" class="flex flex-col items-center justify-center p-4 lg:p-8 rounded-2xl lg:rounded-3xl bg-amber-50 hover:bg-amber-100 transition">
                <div class="text-3xl lg:text-5xl mb-2 lg:mb-4">📋</div>
                <p class="font-black text-center">Pesanan Saya</p>
            </a>
            <button @click="openChatsModal = true" class="flex flex-col items-center justify-center p-4 lg:p-8 rounded-2xl lg:rounded-3xl bg-blue-50 hover:bg-blue-100 transition">
                <div class="text-3xl lg:text-5xl mb-2 lg:mb-4">💬</div>
                <p class="font-black text-center">Chat Seller</p>
            </button>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-3xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6 lg:mb-8">
            <h2 class="text-xl lg:text-2xl font-black text-gray-900">Pesanan Terbaru</h2>
            <a href="{{ route('buyer.orders') }}" class="text-xs lg:text-sm font-bold text-primary-600 hover:text-primary-700">Lihat Semua →</a>
        </div>

        @php
            $recentOrders = auth()->user()->orders()->with('store', 'product')->latest()->take(5)->get();
        @endphp

        <div class="space-y-4">
            @forelse($recentOrders as $order)
                <div class="flex flex-col md:flex-row md:items-center justify-between p-4 lg:p-6 border border-gray-100 rounded-2xl hover:bg-gray-50 transition gap-4">
                    <div class="flex-1">
                        <p class="font-black text-gray-900 text-sm lg:text-base line-clamp-1">{{ $order->product->name ?? 'Pesanan ' . $order->id }}</p>
                    <p class="text-sm text-gray-500">{{ $order->store->name }} • {{ $order->created_at->diffForHumans() }}</p>
                </div>
                    <div class="flex items-center md:text-right gap-4 lg:gap-6 justify-between md:justify-end border-t md:border-t-0 border-gray-100 pt-3 md:pt-0 mt-3 md:mt-0">
                        <div>
                            <p class="text-[10px] lg:text-sm text-gray-500 md:mb-1">Total:</p>
                            <p class="font-black text-sm lg:text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest {{ 
                        ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' :
                        ($order->status === 'accepted' ? 'bg-blue-100 text-blue-700' :
                        ($order->status === 'done' ? 'bg-green-100 text-green-700' :
                        'bg-red-100 text-red-700')))
                    }}">
                        {{ 
                            ($order->status === 'pending' ? 'Menunggu' :
                            ($order->status === 'accepted' ? 'Diproses' :
                            ($order->status === 'done' ? 'Selesai' :
                            'Ditolak')))
                        }}
                    </span>
                    <a href="{{ route('chat.show', $order) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-bold text-xs">
                        💬 Chat
                    </a>
                </div>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 lg:py-16 text-gray-500">
                    <p class="text-base lg:text-lg font-bold mb-4">Belum ada pesanan</p>
                <a href="{{ route('buyer.products') }}" class="inline-block px-8 py-4 bg-primary-600 text-white rounded-2xl font-black hover:bg-primary-700">
                    Mulai Belanja Sekarang
                </a>
            </div>
        @endforelse
        </div>
    </div>
</div>
@endsection