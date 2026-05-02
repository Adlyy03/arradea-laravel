@extends('layouts.dashboard')

@section('title', 'Pesanan Saya - Arradea')
@section('page_title', 'Konfirmasi Pesanan Pelanggan')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-6 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Lacak <span class="text-primary-600 underline underline-offset-4 decoration-4">Pesanan</span> Anda.</h1>
            <p class="text-gray-500 text-base lg:text-lg font-medium leading-relaxed">Terima kasih telah berbelanja di Arradea. Pantau status pengiriman dan riwayat belanja Anda di sini.</p>
        </div>
        <div class="flex gap-4 w-full lg:w-auto">
            <a href="/" class="flex-1 lg:flex-none px-8 py-4 bg-black text-white rounded-2xl font-black text-base shadow-xl hover:scale-105 active:scale-95 transition-all text-center">Belanja Lagi</a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-8">
        <div class="flex justify-between items-center px-4">
            <h2 class="text-3xl font-black text-gray-900 tracking-tighter leading-none">Riwayat <span class="text-primary-600">Belanja</span>.</h2>
            <div class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total: {{ Auth::user()->orders()->count() }}</div>
        </div>
        
        <div class="overflow-x-auto shadow-inner rounded-2xl border border-gray-50">
            <table class="w-full text-left font-['Plus_Jakarta_Sans']">
                <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4">Nomor Pesanan / Toko</th>
                        <th class="px-6 py-4">Total Harga</th>
                        <th class="px-6 py-4">Status Pengiriman</th>
                        <th class="px-6 py-4">Aksi Pembeli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(Auth::user()->orders()->with('store')->latest()->get() as $order)
                        <tr class="hover:bg-primary-50/20 transition duration-300">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 font-black text-xs border border-gray-100 shadow-sm">#ORD</div>
                                    <div class="space-y-1">
                                        <p class="font-black text-base text-gray-900 leading-tight">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-sm font-medium text-gray-400">Toko: {{ $order->store->name ?? 'Arradea Central' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-lg font-black text-gray-900 tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $orderStatusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'accepted' => 'bg-blue-100 text-blue-700',
                                        'done' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'dibatalkan' => 'bg-gray-200 text-gray-700',
                                    ];
                                    $orderStatusLabels = [
                                        'pending' => 'Menunggu',
                                        'accepted' => 'Diproses',
                                        'done' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        'dibatalkan' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="{{ $orderStatusColors[$order->status] ?? 'bg-gray-100 text-gray-500' }} px-5 py-2.5 rounded-3xl text-[10px] font-black uppercase tracking-widest">{{ $orderStatusLabels[$order->status] ?? $order->status }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex gap-3">
                                    <a href="{{ route('chat.show', $order) }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:scale-105 transition-all">Chat Seller</a>
                                    <a href="{{ route('buyer.orders.show', $order) }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-gray-200 transition-all">Detail</a>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" onsubmit="return confirmSubmit(event, @js('Yakin ingin membatalkan pesanan ini?'));">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-red-700 transition-all">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-20 text-center text-gray-400 font-bold space-y-4">
                                <div class="text-5xl mb-4">🛒</div>
                                <p class="text-xl text-gray-900 font-black">Belum Ada Pesanan.</p>
                                <p class="text-sm font-medium">Temukan produk favoritmu dan mulai keranjang belanjamu!</p>
                                <div class="pt-6">
                                    <a href="/" class="px-8 py-3 bg-primary-600 text-white rounded-2xl font-bold text-base inline-block">Cari Produk</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
