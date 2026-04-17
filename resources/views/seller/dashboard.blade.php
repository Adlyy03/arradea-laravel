@extends('layouts.dashboard')

@section('title', 'Dashboard Seller - Arradea')
@section('page_title', 'Performa Toko Penjual')

@section('content')
@php
    $store        = Auth::user()->store;
    $seller       = Auth::user();
    $storeStatus  = $seller->store_status ?? 'closed';
    $pendingCount = $store ? $store->orders()->where('status', 'pending')->count() : 0;
    $doneCount    = $store ? $store->orders()->where('status', 'done')->count() : 0;
    $recentOrders = $store ? $store->orders()->with(['user', 'product'])->latest()->take(5)->get() : collect();
@endphp
<div class="space-y-6 lg:space-y-12">
    <!-- Header Summary -->
    <div class="bg-primary-900 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-accent rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-6 lg:gap-12">
            <div class="space-y-4 text-center lg:text-left">
                <p class="text-[10px] font-black uppercase tracking-widest text-primary-200">Selayang Pandang</p>
                <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-2 lg:mb-4">Toko <span class="text-accent underline underline-offset-4 lg:underline-offset-8">{{ $store->name ?? 'Anda' }}</span>.</h1>
                <h3 class="text-4xl lg:text-6xl font-black text-white/95">{{ $store ? $store->products()->count() : 0 }} Produk</h3>
                <p class="pt-2 text-sm font-black uppercase tracking-widest {{ $storeStatus === 'open' ? 'text-green-300' : 'text-gray-300' }}">
                    Status Toko: {{ $storeStatus === 'open' ? 'Buka' : 'Tutup' }}
                </p>
                <div class="flex items-center gap-4 pt-4 text-sm font-bold text-green-300">
                    <span class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-white">↑</span>
                    <span>{{ $doneCount }} Pesanan berhasil diselesaikan</span>
                </div>
                <div class="pt-2">
                    <form method="POST" action="{{ route('seller.store-status') }}">
                        @csrf
                        <button type="submit" class="px-5 py-3 rounded-2xl font-black text-sm {{ $storeStatus === 'open' ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-700 text-white hover:bg-gray-800' }} transition-all">
                            {{ $storeStatus === 'open' ? 'Tutup Toko' : 'Buka Toko' }}
                        </button>
                    </form>
                </div>
                <form method="POST" action="{{ route('seller.store-schedule') }}" class="mt-5 grid gap-4 max-w-md">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block text-left">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-primary-200 mb-2">Jam Buka</span>
                            <input type="time" name="open_time" value="{{ old('open_time', $seller->open_time) }}" class="w-full rounded-2xl border-0 bg-white/10 text-white placeholder-white/50 focus:ring-2 focus:ring-white/40 px-4 py-3 font-semibold">
                            @error('open_time')
                                <span class="mt-1 block text-xs font-bold text-red-300">{{ $message }}</span>
                            @enderror
                        </label>
                        <label class="block text-left">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-primary-200 mb-2">Jam Tutup</span>
                            <input type="time" name="close_time" value="{{ old('close_time', $seller->close_time) }}" class="w-full rounded-2xl border-0 bg-white/10 text-white placeholder-white/50 focus:ring-2 focus:ring-white/40 px-4 py-3 font-semibold">
                            @error('close_time')
                                <span class="mt-1 block text-xs font-bold text-red-300">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                    <label class="inline-flex items-center gap-3 text-sm font-bold text-white/90">
                        <input type="checkbox" name="auto_schedule" value="1" {{ old('auto_schedule', $seller->auto_schedule ?? true) ? 'checked' : '' }} class="rounded border-white/20 bg-white/10 text-green-500 focus:ring-green-400">
                        Aktifkan Auto Schedule
                    </label>
                    <p class="text-xs font-bold text-primary-200">
                        Jadwal saat ini: {{ $seller->open_time ? substr($seller->open_time, 0, 5) : '--:--' }} - {{ $seller->close_time ? substr($seller->close_time, 0, 5) : '--:--' }}
                        (Auto: {{ ($seller->auto_schedule ?? true) ? 'Aktif' : 'Nonaktif' }})
                    </p>
                    <button type="submit" class="w-full sm:w-max px-5 py-3 rounded-2xl font-black text-sm bg-white text-primary-900 hover:bg-gray-100 transition-all">
                        Simpan Jadwal
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-2 gap-4 w-full lg:w-max">
                <div class="p-6 lg:p-8 bg-white/10 backdrop-blur-xl border border-white/10 rounded-2xl lg:rounded-[2.5rem] text-center">
                    <p class="text-[9px] lg:text-[10px] font-black uppercase tracking-widest text-primary-200 mb-1">Sukses</p>
                    <p class="text-3xl lg:text-4xl font-black leading-none">{{ $doneCount }}</p>
                </div>
                <div class="p-6 lg:p-8 bg-white/10 backdrop-blur-xl border border-white/10 rounded-2xl lg:rounded-[2.5rem] text-center">
                    <p class="text-[9px] lg:text-[10px] font-black uppercase tracking-widest text-primary-200 mb-1">Pending</p>
                    <p class="text-3xl lg:text-4xl font-black leading-none">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-6">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-3xl">📦</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Produk Aktif</p>
                <h4 class="text-4xl font-black text-gray-900 leading-none">{{ $store ? $store->products()->count() : 0 }} Item</h4>
                <a href="/seller/products" class="inline-block pt-4 text-sm font-bold text-primary-600 hover:translate-x-1 transition-all">Kelola Produk &rarr;</a>
            </div>
        </div>
        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-6">
            <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-3xl">🕒</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Order Menunggu</p>
                <h4 class="text-4xl font-black text-gray-900 leading-none">{{ $pendingCount }} Order</h4>
                <a href="/seller/orders" class="inline-block pt-4 text-sm font-bold text-accent hover:translate-x-1 transition-all">Segera Proses &rarr;</a>
            </div>
        </div>
        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-6">
            <div class="w-16 h-16 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-3xl">✅</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Pesanan Selesai</p>
                <h4 class="text-4xl font-black text-gray-900 leading-none">{{ $doneCount }} Selesai</h4>
                <p class="pt-4 text-sm font-bold text-gray-400 uppercase tracking-widest">Total Riwayat Sukses</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table (dari DB nyata) -->
    <div class="space-y-8">
        <div class="flex justify-between items-end px-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Pesanan <span class="text-primary-600">Terbaru</span>.</h2>
                <p class="text-gray-400 font-medium">Monitoring transaksi masuk dari pelanggan Anda.</p>
            </div>
            <a href="/seller/orders" class="text-sm font-black text-primary-600 uppercase tracking-widest border-b-2 border-primary-600 pb-1">Lihat Semua</a>
        </div>

        <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-[10px] font-black tracking-widest uppercase text-gray-400">
                    <tr>
                        <th class="px-5 lg:px-10 py-8">Pelanggan</th>
                        <th class="px-5 lg:px-10 py-8">Produk Dipesan</th>
                        <th class="px-5 lg:px-10 py-8">Total Bayar</th>
                        <th class="px-5 lg:px-10 py-8">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 lg:px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-black">
                                        {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900">{{ $order->user->name ?? 'Pembeli' }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 lg:px-10 py-8">
                                <p class="font-bold text-gray-900 text-sm">{{ $order->product->name ?? 'Produk' }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">Qty: {{ $order->quantity ?? 1 }}</p>
                            </td>
                            <td class="px-5 lg:px-10 py-8 text-lg font-black text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="px-5 lg:px-10 py-8">
                                @php
                                    $statusColors = [
                                        'pending'  => 'bg-amber-100 text-amber-700',
                                        'accepted' => 'bg-blue-100 text-blue-700',
                                        'done'     => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $statusLabels = [
                                        'pending'  => 'Menunggu',
                                        'accepted' => 'Diproses',
                                        'done'     => 'Selesai',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="{{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-500' }} px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 lg:px-20 py-12 lg:py-24 text-center text-gray-400 font-bold">
                                <div class="text-3xl lg:text-5xl mb-4">📭</div>
                                <p class="text-xl text-gray-900 font-black">Belum Ada Pesanan Masuk.</p>
                                <p class="text-sm font-medium mt-2">Pesanan dari pembeli akan muncul di sini secara otomatis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
