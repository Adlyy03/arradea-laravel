@extends('layouts.dashboard')

@section('title', 'Detail Pesanan - Arradea')
@section('page_title', 'Detail Pesanan Belanja')

@section('content')
<div class="space-y-4 lg:space-y-12">
    @php
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Sedang Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        $statusClasses = [
            'pending' => 'amber',
            'processing' => 'blue',
            'shipped' => 'purple',
            'completed' => 'green',
            'cancelled' => 'gray',
        ];
        $paymentLabels = [
            'cod' => 'COD',
            'qris' => 'QRIS Manual',
        ];
        $paymentStatusLabels = [
            'pending' => 'Menunggu Bukti',
            'waiting_confirmation' => 'Menunggu Konfirmasi Seller',
            'paid' => 'Sudah Dibayar',
            'rejected' => 'Ditolak',
        ];
    @endphp
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-3 lg:gap-10 bg-white p-5 lg:p-10 lg:p-20 rounded-xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-2 lg:mb-4">Pesanan <span class="text-primary-600 underline underline-offset-2 lg:underline-offset-8 decoration-2 lg:decoration-8">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span></h1>
            <p class="text-gray-500 text-xs lg:text-lg font-medium leading-relaxed">Status: <span class="font-black text-{{ $statusClasses[$order->status] ?? 'amber' }}-600">{{ $statusLabels[$order->status] ?? strtoupper($order->status) }}</span></p>
            <p class="text-gray-500 text-xs lg:text-lg font-medium leading-relaxed mt-1">Pembayaran: <span class="font-black text-gray-900">{{ $paymentLabels[$order->payment_method ?? 'cod'] ?? strtoupper($order->payment_method ?? 'cod') }}</span> <span class="ml-1 font-black text-{{ ($order->payment_method === 'qris' && $order->payment_status === 'waiting_confirmation') ? 'amber' : (($order->payment_status === 'paid') ? 'green' : (($order->payment_status === 'rejected') ? 'red' : 'gray')) }}-600">{{ $paymentStatusLabels[$order->payment_status ?? 'pending'] ?? strtoupper($order->payment_status ?? 'pending') }}</span></p>
        </div>
        <div class="flex gap-2 lg:gap-4 w-full lg:w-auto">
            <a href="{{ route('buyer.orders') }}" class="flex-1 lg:flex-none px-4 lg:px-5 lg:px-10 py-2.5 lg:py-5 bg-black text-white rounded-xl lg:rounded-2xl lg:rounded-3xl font-black text-sm lg:text-lg shadow-xl hover:scale-105 active:scale-95 transition-all text-center">← Kembali</a>
            @if($order->status === 'pending')
                <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" onsubmit="return confirmSubmit(event, @js('Yakin ingin membatalkan pesanan ini?'));" class="flex-1 lg:flex-none">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full px-4 lg:px-10 py-2.5 lg:py-5 bg-red-600 text-white rounded-xl lg:rounded-3xl font-black text-sm lg:text-lg shadow-xl hover:bg-red-700 transition-all text-center">Batalkan</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Order Details -->
    <div class="grid lg:grid-cols-3 gap-4 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-8">
            <!-- Product Info -->
            <div class="bg-white rounded-xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-5 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
                <h2 class="text-base lg:text-4xl font-black text-gray-900 tracking-tighter mb-4 lg:mb-8">Produk <span class="text-primary-600">Pesanan</span></h2>
                
                <div class="flex flex-col lg:flex-row gap-4 lg:gap-8 items-start">
                    <div class="w-full lg:w-48 h-40 lg:h-48 rounded-xl lg:rounded-[2rem] overflow-hidden shadow-md border border-gray-100 flex-shrink-0">
                        <img src="{{ $order->product?->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop' }}" alt="{{ $order->product?->name }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 space-y-3 lg:space-y-4">
                        <div>
                            <p class="text-lg lg:text-4xl font-black text-gray-900 tracking-tight">{{ $order->product?->name ?? 'Produk (Dihapus)' }}</p>
                            <p class="text-xs lg:text-sm text-gray-400 font-medium mt-1 lg:mt-2">Toko: {{ $order->store->name ?? 'Arradea Central' }}</p>
                        </div>

                        <div class="space-y-1 lg:space-y-2 pt-2 lg:pt-4">
                            <p class="text-xs lg:text-sm font-medium text-gray-500">Deskripsi:</p>
                            <p class="text-sm lg:text-base font-medium text-gray-700 leading-relaxed">{{ $order->product?->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 lg:gap-4 pt-3 lg:pt-6">
                            <div class="bg-gray-50 rounded-xl lg:rounded-2xl p-3 lg:p-4">
                                <p class="text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Satuan</p>
                                <p class="text-base lg:text-2xl font-black text-gray-900 mt-0.5 lg:mt-1">Rp {{ number_format($order->product?->price ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl lg:rounded-2xl p-3 lg:p-4">
                                <p class="text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah</p>
                                <p class="text-base lg:text-2xl font-black text-gray-900 mt-0.5 lg:mt-1">{{ $order->quantity }} Item</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="bg-white rounded-xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-5 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4 lg:mb-8">
                    <h2 class="text-base lg:text-4xl font-black text-gray-900 tracking-tighter">Chat <span class="text-primary-600">Penjual</span></h2>
                    <a href="{{ route('chat.show', $order) }}" class="px-4 lg:px-6 py-2 lg:py-3 bg-primary-600 text-white rounded-xl lg:rounded-2xl text-xs lg:text-sm font-black hover:scale-105 transition-all">Buka →</a>
                </div>
                
                <div class="bg-gray-50 rounded-xl lg:rounded-2xl p-4 lg:p-6 text-center text-gray-500">
                    <p class="text-xs lg:text-sm font-medium">Masuk ke chat untuk berkomunikasi dengan penjual tentang pesanan ini.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Payment Method -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl lg:rounded-3xl p-5 lg:p-8 shadow-sm border border-green-200">
                <h3 class="text-sm lg:text-lg font-black text-gray-900 mb-3 lg:mb-4 flex items-center gap-2">
                    <span class="text-xl">💳</span>
                    Metode Pembayaran
                </h3>
                <p class="text-xs lg:text-sm font-bold text-green-700 leading-relaxed">Pesanan ini menggunakan metode <span class="text-green-900">COD (Cash on Delivery)</span>.</p>
                <p class="text-xs lg:text-sm font-medium text-green-600 mt-2 leading-relaxed">Pembayaran dilakukan saat barang diterima.</p>
            </div>

            <!-- Status Timeline -->
            <div class="bg-white rounded-xl lg:rounded-3xl p-5 lg:p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm lg:text-lg font-black text-gray-900 mb-4 lg:mb-6">Status Pesanan</h3>
                
                <div class="space-y-4">
                    @php
                        $statuses = [
                            'pending'    => 'Menunggu Konfirmasi',
                            'processing' => 'Diproses Penjual',
                            'shipped'    => 'Sedang Dikirim',
                            'completed'  => 'Selesai',
                            'cancelled'  => 'Dibatalkan',
                        ];
                        $statusColors = [
                            'pending'    => 'amber',
                            'processing' => 'blue',
                            'shipped'    => 'purple',
                            'completed'  => 'green',
                            'cancelled'  => 'gray',
                        ];
                        $timelineStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
                    @endphp

                    @foreach($timelineStatuses as $status)
                        <div class="flex items-start gap-2 lg:gap-3">
                            <div class="w-5 lg:w-6 h-5 lg:h-6 rounded-full {{$order->status === $status || 
                                ($status === 'completed' && $order->status === 'completed') || 
                                ($status === 'shipped' && in_array($order->status, ['shipped', 'completed'])) ||
                                ($status === 'processing' && in_array($order->status, ['processing', 'shipped', 'completed'])) 
                                ? 'bg-' . $statusColors[$status] . '-600' 
                                : 'bg-gray-200'}} flex-shrink-0 mt-0.5 lg:mt-1"></div>
                            <div>
                                <p class="font-black text-xs lg:text-sm text-gray-900">{{ $statuses[$status] }}</p>
                                @if($status === 'pending' && $order->status === 'pending')
                                    <p class="text-[9px] lg:text-[10px] text-gray-400 mt-0.5 lg:mt-1">Sedang menunggu respons penjual...</p>
                                @elseif($status === 'processing' && in_array($order->status, ['processing', 'shipped', 'completed']))
                                    <p class="text-[9px] lg:text-[10px] text-gray-400 mt-0.5 lg:mt-1">Penjual menerima pesanan ini.</p>
                                @elseif($status === 'shipped' && in_array($order->status, ['shipped', 'completed']))
                                    <p class="text-[9px] lg:text-[10px] text-gray-400 mt-0.5 lg:mt-1">Pesanan sedang dalam pengiriman.</p>
                                @elseif($status === 'completed' && $order->status === 'completed')
                                    <p class="text-[9px] lg:text-[10px] text-gray-400 mt-0.5 lg:mt-1">Pesanan sudah selesai.</p>
                                @elseif($status === 'cancelled' && $order->status === 'cancelled')
                                    <p class="text-[9px] lg:text-[10px] text-gray-400 mt-0.5 lg:mt-1">Pesanan dibatalkan.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-xl lg:rounded-3xl p-5 lg:p-8 shadow-sm border border-gray-100">
                <h3 class="text-sm lg:text-lg font-black text-gray-900 mb-4 lg:mb-6">Resume Pesanan</h3>
                
                <div class="space-y-2 lg:space-y-3 border-b border-gray-100 pb-3 lg:pb-4 mb-3 lg:mb-4">
                    <div class="flex justify-between items-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">Subtotal</p>
                        <p class="font-black text-xs lg:text-base text-gray-900">Rp {{ number_format($order->product?->price ?? 0 * $order->quantity, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-xs lg:text-sm font-medium text-gray-500">PPN (Estimasi)</p>
                        <p class="font-black text-xs lg:text-base text-gray-900">Rp {{ number_format(($order->product?->price ?? 0 * $order->quantity) * 0.1, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <p class="text-base lg:text-lg font-black text-gray-900">Total</p>
                    <p class="text-xl lg:text-3xl font-black text-primary-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>

            @if($order->payment_method === 'qris')
                <div class="bg-white rounded-xl lg:rounded-3xl p-5 lg:p-8 shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm lg:text-lg font-black text-gray-900">Pembayaran QRIS</h3>
                        <span class="px-3 py-1.5 rounded-full text-[10px] lg:text-xs font-black uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : ($order->payment_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $paymentStatusLabels[$order->payment_status ?? 'pending'] ?? strtoupper($order->payment_status ?? 'pending') }}</span>
                    </div>

                    @if($order->store?->user?->qris_image)
                        <div class="rounded-2xl border border-gray-200 overflow-hidden bg-gray-50">
                            <img src="{{ asset('storage/'.$order->store->user->qris_image) }}" alt="QRIS seller" class="w-full max-w-sm mx-auto object-contain">
                        </div>
                    @endif

                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4 space-y-2 text-sm text-gray-700">
                        <p><span class="font-black text-gray-900">Penerima:</span> {{ $order->store?->user?->payment_name ?? '-' }}</p>
                        <p><span class="font-black text-gray-900">Jenis:</span> {{ strtoupper($order->store?->user?->payment_type ?? 'qris') }}</p>
                        <p><span class="font-black text-gray-900">Nominal:</span> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>

                    @if($order->payment_proof)
                        <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white">
                            <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti pembayaran" class="w-full h-auto object-cover">
                        </div>
                    @endif

                    @if(in_array($order->payment_status, ['pending', 'waiting_confirmation', 'rejected']))
                        <form action="{{ route('buyer.orders.payment-proof', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs lg:text-sm font-black text-gray-700 mb-2">Upload Bukti Transfer</label>
                                <input type="file" name="payment_proof" accept="image/*" required class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-primary-600 file:text-white file:font-black">
                            </div>
                            <button type="submit" class="w-full px-4 py-3 rounded-2xl bg-primary-600 text-white font-black text-sm lg:text-base hover:bg-primary-700 transition-all active:scale-95">Upload Bukti Pembayaran</button>
                        </form>
                    @else
                        <p class="text-xs lg:text-sm text-gray-500">Pembayaran sudah diproses seller.</p>
                    @endif

                    @if($order->rejected_reason)
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="font-black mb-1">Alasan penolakan</p>
                            <p>{{ $order->rejected_reason }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Order Info -->
            <div class="bg-white rounded-xl lg:rounded-3xl p-5 lg:p-8 shadow-sm border border-gray-100 space-y-2 lg:space-y-3 text-xs lg:text-sm">
                <div>
                    <p class="text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 lg:mb-1">Nomor Pesanan</p>
                    <p class="font-black text-gray-900">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 lg:mb-1">Tanggal</p>
                    <p class="font-medium text-gray-700">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 lg:mb-1">Toko</p>
                    <p class="font-black text-gray-900">{{ $order->store->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
