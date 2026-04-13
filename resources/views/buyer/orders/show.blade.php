@extends('layouts.dashboard')

@section('title', 'Detail Pesanan - Arradea')
@section('page_title', 'Detail Pesanan Belanja')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Pesanan <span class="text-primary-600 underline underline-offset-4 lg:underline-offset-8 decoration-4 lg:decoration-8">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>.</h1>
            <p class="text-gray-500 text-base lg:text-lg font-medium leading-relaxed">Status: <span class="font-black text-{{ $order->status === 'done' ? 'green' : ($order->status === 'rejected' ? 'red' : ($order->status === 'accepted' ? 'blue' : ($order->status === 'dibatalkan' ? 'gray' : 'amber'))) }}-600">{{ strtoupper($order->status) }}</span></p>
        </div>
        <div class="flex gap-4 w-full lg:w-auto">
            <a href="{{ route('buyer.orders') }}" class="flex-1 lg:flex-none px-8 lg:px-5 lg:px-10 py-5 bg-black text-white rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl hover:scale-105 active:scale-95 transition-all text-center">← Kembali</a>
            @if($order->status === 'pending')
                <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');" class="flex-1 lg:flex-none">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full px-8 lg:px-10 py-5 bg-red-600 text-white rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl hover:bg-red-700 transition-all text-center">Batalkan Pesanan</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Order Details -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Product Info -->
            <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
                <h2 class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tighter mb-8">Produk <span class="text-primary-600">Pesanan</span>.</h2>
                
                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    <div class="w-full lg:w-48 h-48 rounded-[2rem] overflow-hidden shadow-md border border-gray-100 flex-shrink-0">
                        <img src="{{ $order->product?->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop' }}" alt="{{ $order->product?->name }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 space-y-4">
                        <div>
                            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ $order->product?->name ?? 'Produk (Dihapus)' }}</p>
                            <p class="text-sm text-gray-400 font-medium mt-2">Toko: {{ $order->store->name ?? 'Arradea Central' }}</p>
                        </div>

                        <div class="space-y-2 pt-4">
                            <p class="text-sm font-medium text-gray-500">Deskripsi:</p>
                            <p class="text-base font-medium text-gray-700 leading-relaxed">{{ $order->product?->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-6">
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Satuan</p>
                                <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($order->product?->price ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah</p>
                                <p class="text-2xl font-black text-gray-900 mt-1">{{ $order->quantity }} Item</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tighter">Chat dengan <span class="text-primary-600">Penjual</span>.</h2>
                    <a href="{{ route('chat.show', $order) }}" class="px-6 py-3 bg-primary-600 text-white rounded-2xl text-sm font-black hover:scale-105 transition-all">Buka Chat →</a>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-6 text-center text-gray-500">
                    <p class="text-sm font-medium">Masuk ke chat untuk berkomunikasi dengan penjual tentang pesanan ini.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-2xl lg:rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-6">Status Pesanan</h3>
                
                <div class="space-y-4">
                    @php
                        $statuses = ['pending' => 'Menunggu Konfirmasi', 'accepted' => 'Diterima Penjual', 'rejected' => 'Ditolak', 'done' => 'Selesai', 'dibatalkan' => 'Dibatalkan Pembeli'];
                        $statusColors = ['pending' => 'amber', 'accepted' => 'blue', 'rejected' => 'red', 'done' => 'green', 'dibatalkan' => 'gray'];
                    @endphp

                    @foreach(['pending', 'accepted', 'rejected', 'done', 'dibatalkan'] as $status)
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full {{$order->status === $status || 
                                ($status === 'done' && $order->status === 'done') || 
                                ($status === 'accepted' && in_array($order->status, ['accepted', 'done'])) 
                                ? 'bg-' . $statusColors[$status] . '-600' 
                                : 'bg-gray-200'}} flex-shrink-0 mt-1"></div>
                            <div>
                                <p class="font-black text-sm text-gray-900">{{ $statuses[$status] }}</p>
                                @if($status === 'pending' && $order->status === 'pending')
                                    <p class="text-[10px] text-gray-400 mt-1">Sedang menunggu respons penjual...</p>
                                @elseif($status === 'accepted' && in_array($order->status, ['accepted', 'done']))
                                    <p class="text-[10px] text-gray-400 mt-1">Penjual menerima pesanan ini.</p>
                                @elseif($status === 'dibatalkan' && $order->status === 'dibatalkan')
                                    <p class="text-[10px] text-gray-400 mt-1">Pesanan dibatalkan oleh pembeli.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl lg:rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-6">Resume Pesanan</h3>
                
                <div class="space-y-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-500">Subtotal</p>
                        <p class="font-black text-gray-900">Rp {{ number_format($order->product?->price ?? 0 * $order->quantity, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-500">PPN (Estimasi)</p>
                        <p class="font-black text-gray-900">Rp {{ number_format(($order->product?->price ?? 0 * $order->quantity) * 0.1, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <p class="text-lg font-black text-gray-900">Total</p>
                    <p class="text-3xl font-black text-primary-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Order Info -->
            <div class="bg-white rounded-2xl lg:rounded-3xl p-8 shadow-sm border border-gray-100 space-y-3 text-sm">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nomor Pesanan</p>
                    <p class="font-black text-gray-900">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal</p>
                    <p class="font-medium text-gray-700">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Toko</p>
                    <p class="font-black text-gray-900">{{ $order->store->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
