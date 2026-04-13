@extends('layouts.dashboard')

@section('title', 'Order Masuk - Arradea Seller')
@section('page_title', 'Manajemen Order Masuk')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Order <span class="text-primary-600 underline underline-offset-4 lg:underline-offset-8 decoration-4 lg:decoration-8">Masuk</span> Toko.</h1>
            <p class="text-gray-500 text-base lg:text-lg font-medium leading-relaxed">Kelola semua pesanan yang masuk ke toko Anda. Konfirmasi atau tolak pesanan untuk memperbarui status pembeli.</p>
        </div>
        <div class="flex gap-4 shrink-0 w-full lg:w-auto">
            <div class="flex-1 lg:flex-none px-6 lg:px-5 lg:px-10 py-5 bg-amber-50 text-amber-700 rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-center">
                <p class="text-2xl lg:text-3xl">{{ $pendingCount }}</p>
                <p class="text-[10px] uppercase tracking-widest">Pending</p>
            </div>
            <div class="flex-1 lg:flex-none px-6 lg:px-5 lg:px-10 py-5 bg-green-50 text-green-700 rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-center">
                <p class="text-2xl lg:text-3xl">{{ $doneCount }}</p>
                <p class="text-[10px] uppercase tracking-widest">Selesai</p>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex justify-between items-center px-8 lg:px-6 lg:px-12 py-8 lg:py-5 lg:py-10 border-b border-gray-50">
            <h2 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight">Seluruh <span class="text-primary-600">Pesanan</span>.</h2>
            <div class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total: {{ $orders->total() }}</div>
        </div>

        <div class="overflow-x-auto">

        <table class="w-full text-left">
            <thead class="bg-gray-50/50 text-[10px] font-black tracking-widest uppercase text-gray-400">
                <tr>
                    <th class="px-5 lg:px-10 py-8">No. Order / Pembeli</th>
                    <th class="px-5 lg:px-10 py-8">Produk</th>
                    <th class="px-5 lg:px-10 py-8">Catatan Pembeli</th>
                    <th class="px-5 lg:px-10 py-8">Qty</th>
                    <th class="px-5 lg:px-10 py-8">Total Bayar</th>
                    <th class="px-5 lg:px-10 py-8">Status</th>
                    <th class="px-5 lg:px-10 py-8">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                    <tr class="hover:bg-primary-50/20 transition duration-300">
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-black shrink-0">
                                    {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900">{{ $order->user->name ?? 'Pembeli' }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <p class="font-bold text-gray-900">{{ $order->product->name ?? '—' }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Varian: {{ data_get($order->product?->getVariant($order->variant_key), 'name', 'Default') }}</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <p class="max-w-xs text-sm font-medium text-gray-600 line-clamp-2">{{ $order->notes ?: '—' }}</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <p class="text-gray-700 font-black text-lg">{{ $order->quantity ?? 1 }}x</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            @if(($order->discount_percent_applied ?? 0) > 0 && $order->unit_price_original)
                                <p class="text-[10px] font-bold text-gray-400 line-through">Rp {{ number_format($order->unit_price_original * $order->quantity, 0, ',', '.') }}</p>
                            @endif
                            <p class="text-xl font-black text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            @php
                                $colors = [
                                    'pending'  => 'bg-amber-100 text-amber-700',
                                    'accepted' => 'bg-blue-100 text-blue-700',
                                    'done'     => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'dibatalkan' => 'bg-gray-200 text-gray-700',
                                ];
                                $labels = [
                                    'pending'  => 'Menunggu',
                                    'accepted' => 'Diproses',
                                    'done'     => 'Selesai',
                                    'rejected' => 'Ditolak',
                                    'dibatalkan' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="{{ $colors[$order->status] ?? 'bg-gray-100 text-gray-500' }} px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                                {{ $labels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex gap-3">
                                @if($order->status === 'pending')
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-xs font-black shadow-lg shadow-primary-100 hover:scale-105 transition-all">
                                            ✓ Terima
                                        </button>
                                    </form>
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-5 py-2.5 bg-red-100 text-red-600 rounded-xl text-xs font-black hover:bg-red-200 transition-all">
                                            ✕ Tolak
                                        </button>
                                    </form>
                                @elseif($order->status === 'accepted')
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-xs font-black shadow-lg shadow-green-100 hover:scale-105 transition-all">
                                            ✓ Selesaikan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 font-bold italic px-4 py-2">—</span>
                                @endif
                                <a href="{{ route('chat.show', $order) }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-black hover:bg-blue-700 transition-all">💬 Chat</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-10 lg:px-20 py-16 lg:py-32 text-center text-gray-400 font-bold">
                            <div class="text-4xl lg:text-6xl mb-6">📭</div>
                            <p class="text-2xl text-gray-900 font-black">Belum Ada Pesanan Masuk.</p>
                            <p class="text-sm font-medium mt-2">Pesanan dari pembeli akan muncul di sini secara otomatis.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($orders->hasPages())
            <div class="px-8 lg:px-6 lg:px-12 py-6 lg:py-8 border-t border-gray-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
