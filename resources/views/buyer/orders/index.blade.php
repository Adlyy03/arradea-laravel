@extends('layouts.dashboard')

@section('title', 'Pesanan Saya - Arradea')
@section('page_title', 'Pesanan Saya')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl text-green-800 bg-green-50 border border-green-200">{{ session('success') }}</div>
        @endif

        @php
            $orders = Auth::user()->orders()->with(['product', 'store'])->latest()->get();
        @endphp

        @forelse($orders as $order)
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-6 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition rounded-2xl">
                <img src="{{ $order->product->image ?? 'https://via.placeholder.com/100?text=No+Image' }}" alt="{{ $order->product->name }}" class="w-20 h-20 rounded-2xl object-cover shadow-sm">

                <div class="flex-1">
                    <h3 class="text-lg font-black text-gray-900 leading-tight">{{ $order->product->name }}</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $order->store->name }}</p>
                    <p class="text-sm font-bold text-primary-600 mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Qty: {{ $order->quantity }} | {{ $order->created_at->format('d M Y') }}</p>
                </div>

                <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest
                        @if($order->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($order->status === 'accepted') bg-blue-100 text-blue-700
                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-700
                        @elseif($order->status === 'delivered') bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ $order->status }}
                    </span>

                    <a href="{{ route('buyer.orders.show', $order) }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition text-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 lg:py-24 text-gray-400">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black mb-4">Belum ada pesanan</h3>
                <p class="text-gray-500 mb-6">Mulai berbelanja dan pesan produk favorit Anda</p>
                <a href="{{ route('buyer.products') }}" class="inline-block px-6 py-3 bg-primary-600 text-white rounded-2xl font-black hover:bg-primary-700 transition">
                    Mulai Belanja
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection