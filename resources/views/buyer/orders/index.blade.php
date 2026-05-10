@extends('layouts.dashboard')
@section('title', 'Pesanan Saya — Arradea')
@section('page_title', 'Pesanan Saya')

@section('content')
@php
    $orders = Auth::user()->orders()->with(['product','store'])->latest()->get();
    $statusMap = [
        'pending'    => ['Menunggu',   'bg-amber-100 text-amber-700'],
        'accepted'   => ['Diproses',   'bg-blue-100 text-blue-700'],
        'shipped'    => ['Dikirim',    'bg-purple-100 text-purple-700'],
        'done'       => ['Selesai',    'bg-green-100 text-green-700'],
        'rejected'   => ['Ditolak',    'bg-red-100 text-red-700'],
        'dibatalkan' => ['Dibatalkan', 'bg-gray-100 text-gray-500'],
    ];
@endphp

<div class="space-y-5 fade-up">

    {{-- Filter tabs --}}
    <div class="flex gap-1.5 lg:gap-2 overflow-x-auto pb-1 scrollbar-none -mx-5 px-5 lg:mx-0 lg:px-0">
        @php $activeStatus = request('status'); @endphp
        <a href="{{ route('buyer.orders') }}" class="flex-shrink-0 px-3 lg:px-4 py-1.5 lg:py-2 rounded-lg lg:rounded-xl text-[10px] lg:text-xs font-bold transition {{ !$activeStatus ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}" style="{{ !$activeStatus ? 'background:#72bf77' : '' }}">Semua</a>
        @foreach(['pending'=>'Menunggu','accepted'=>'Diproses','shipped'=>'Dikirim','done'=>'Selesai','rejected'=>'Ditolak'] as $key=>$label)
        <a href="{{ route('buyer.orders', ['status'=>$key]) }}" class="flex-shrink-0 px-3 lg:px-4 py-1.5 lg:py-2 rounded-lg lg:rounded-xl text-[10px] lg:text-xs font-bold transition {{ $activeStatus===$key ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}" style="{{ $activeStatus===$key ? 'background:#72bf77' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- Order list --}}
    <div class="space-y-2 lg:space-y-3">
        @php
            if(request('status')) $orders = $orders->where('status', request('status'));
        @endphp
        @forelse($orders as $order)
        @php [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status,'bg-gray-100 text-gray-600']; @endphp
        <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4 sm:p-5 flex flex-col sm:flex-row gap-3 lg:gap-4 hover:shadow-md hover:border-green-200/50 transition-all">
            <img src="{{ $order->product->image ?? 'https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk' }}"
                alt="{{ $order->product->name }}"
                class="w-16 lg:w-20 h-16 lg:h-20 rounded-lg lg:rounded-xl object-cover flex-shrink-0 self-center sm:self-start"
                onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-1.5 lg:gap-2 mb-0.5 lg:mb-1">
                    <span class="text-[9px] lg:text-[10px] font-bold uppercase tracking-widest" style="color:#72bf77">🏪 {{ $order->store->name ?? '—' }}</span>
                    <span class="px-2 lg:px-2.5 py-0.5 rounded-md lg:rounded-lg text-[9px] lg:text-[10px] font-black uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <h3 class="font-black text-gray-900 leading-tight mb-0.5 lg:mb-1 text-sm lg:text-base">{{ $order->product->name }}</h3>
                <div class="flex flex-wrap gap-2 lg:gap-3 text-[10px] lg:text-xs text-gray-400">
                    <span>Qty: <strong class="text-gray-700">{{ $order->quantity }}</strong></span>
                    <span>·</span>
                    <span>{{ $order->created_at->format('d M Y') }}</span>
                    <span class="hidden sm:inline">·</span>
                    <span class="hidden sm:inline">{{ $order->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 lg:gap-3 flex-shrink-0">
                <div class="text-right">
                    <p class="text-[10px] lg:text-xs text-gray-400">Total</p>
                    <p class="font-black text-gray-900 text-base lg:text-lg">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                </div>
                <div class="flex gap-1 lg:gap-1.5">
                    <a href="{{ route('buyer.orders.show', $order) }}" class="px-2.5 lg:px-3 py-1.5 rounded-lg text-[10px] lg:text-xs font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Detail</a>
                    <a href="{{ route('chat.show', $order) }}" class="px-2.5 lg:px-3 py-1.5 rounded-lg text-[10px] lg:text-xs font-bold text-white transition hover:opacity-80" style="background:#72bf77">Chat</a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 flex flex-col items-center justify-center py-16 lg:py-20 text-center">
            <span class="text-4xl lg:text-5xl mb-3 lg:mb-4">📋</span>
            <h3 class="text-lg lg:text-xl font-black text-gray-900 mb-1 lg:mb-2">Belum ada pesanan</h3>
            <p class="text-xs lg:text-sm text-gray-400 mb-4 lg:mb-6">Mulai berbelanja dan lacak pesananmu di sini.</p>
            <a href="{{ route('buyer.products') }}" class="px-5 lg:px-6 py-2.5 lg:py-3 rounded-xl lg:rounded-2xl font-bold text-xs lg:text-sm text-white transition hover:opacity-90" style="background:#72bf77">Mulai Belanja</a>
        </div>
        @endforelse
    </div>
</div>
@endsection