@extends('layouts.dashboard')

@section('title', 'Pesanan Saya - Arradea')
@section('page_title', 'Konfirmasi Pesanan Pelanggan')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Lacak <span class="text-primary-600 underline underline-offset-4 lg:underline-offset-8 decoration-4 lg:decoration-8">Pesanan</span> Anda.</h1>
            <p class="text-gray-500 text-base lg:text-lg font-medium leading-relaxed">Terima kasih telah berbelanja di Arradea. Pantau status pengiriman dan riwayat belanja Anda di sini.</p>
        </div>
        <div class="flex gap-4 w-full lg:w-auto">
            <a href="/" class="flex-1 lg:flex-none px-8 lg:px-5 lg:px-10 py-5 bg-black text-white rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl hover:scale-105 active:scale-95 transition-all text-center">Belanja Lagi</a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-6 lg:p-12 shadow-sm border border-gray-100 space-y-8 lg:space-y-6 lg:space-y-12">
        <div class="flex justify-between items-center px-4">
            <h2 class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tighter leading-none">Riwayat <span class="text-primary-600">Belanja</span>.</h2>
            <div class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total: {{ Auth::user()->orders()->count() }}</div>
        </div>
        
        <div class="overflow-x-auto shadow-inner rounded-2xl lg:rounded-3xl lg:rounded-[3rem] border border-gray-50">
            <table class="w-full text-left font-['Plus_Jakarta_Sans']">
                <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Nomor Pesanan / Toko</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Total Harga</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Status Pengiriman</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Aksi Pembeli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(Auth::user()->orders()->with('store')->latest()->get() as $order)
                        <tr class="hover:bg-primary-50/20 transition duration-300">
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 font-black text-xs border border-gray-100 shadow-sm">#ORD</div>
                                    <div class="space-y-1">
                                        <p class="font-black text-xl text-gray-900 leading-tight">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-sm font-medium text-gray-400 italic">Toko: {{ $order->store->name ?? 'Arradea Central' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <p class="text-2xl font-black text-gray-900 tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
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
                                <span class="{{ $orderStatusColors[$order->status] ?? 'bg-gray-100 text-gray-500' }} px-5 py-2.5 rounded-2xl lg:rounded-3xl text-[10px] font-black uppercase tracking-widest">{{ $orderStatusLabels[$order->status] ?? $order->status }}</span>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <div class="flex gap-4">
                                    <a href="{{ route('chat.show', $order) }}" class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.05] transition-all">Chat Seller</a>
                                    <a href="{{ route('buyer.orders.show', $order) }}" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Detail Pesanan</a>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 lg:px-20 py-16 lg:py-32 text-center text-gray-400 font-bold space-y-4">
                                <div class="text-4xl lg:text-6xl mb-6">🛒</div>
                                <p class="text-2xl text-gray-900 font-black">Belum Ada Pesanan.</p>
                                <p class="text-sm font-medium">Temukan produk favoritmu dan mulai keranjang belanjamu!</p>
                                <div class="pt-8">
                                    <a href="/" class="px-5 lg:px-10 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-3xl font-black text-lg">Cari Produk</a>
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
