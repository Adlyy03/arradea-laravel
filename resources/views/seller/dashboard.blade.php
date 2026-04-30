@extends('layouts.dashboard')
@section('title', 'Dashboard Seller — Arradea')
@section('page_title', 'Dashboard Seller')

@section('content')
@php
    $store        = Auth::user()->store;
    $seller       = Auth::user();
    $storeStatus  = $seller->store_status ?? 'closed';
    $pendingCount = $store ? $store->orders()->where('status','pending')->count() : 0;
    $acceptedCount= $store ? $store->orders()->where('status','accepted')->count() : 0;
    $doneCount    = $store ? $store->orders()->where('status','done')->count() : 0;
    $productCount = $store ? $store->products()->count() : 0;
    $recentOrders = $store ? $store->orders()->with(['user','product'])->latest()->take(5)->get() : collect();
@endphp

<div class="space-y-5 fade-up">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Seller Center</p>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">
                    {{ $store->name ?? 'Toko '.$seller->name }}
                    <span class="inline-flex items-center gap-1.5 ml-2 px-3 py-1 rounded-full text-xs font-black {{ $storeStatus==='open' ? '' : '' }}" style="{{ $storeStatus==='open' ? 'background:rgba(34,197,94,.2);color:#4ade80' : 'background:rgba(255,255,255,.1);color:#9ca3af' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $storeStatus==='open' ? 'bg-green-400' : 'bg-gray-500' }} animate-pulse"></span>
                        {{ $storeStatus==='open' ? 'Buka' : 'Tutup' }}
                    </span>
                </h1>
                <p class="text-white/50 text-sm mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('seller.store-status') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold transition hover:opacity-90"
                        style="{{ $storeStatus==='open' ? 'background:rgba(220,38,38,.2);color:#f87171;border:1px solid rgba(220,38,38,.3)' : 'background:rgba(114,191,119,.2);color:#72bf77;border:1px solid rgba(114,191,119,.3)' }}">
                        {{ $storeStatus==='open' ? '🔴 Tutup Toko' : '🟢 Buka Toko' }}
                    </button>
                </form>
                <a href="{{ route('seller.products.create') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">+ Tambah Produk</a>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6" style="border-top:1px solid rgba(255,255,255,.08)">
            <div class="text-center text-white">
                <p class="text-2xl font-black">{{ $productCount }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Produk</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-amber-400">{{ $pendingCount }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Menunggu</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-blue-400">{{ $acceptedCount }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Diproses</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-green-400">{{ $doneCount }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Selesai</p>
            </div>
        </div>
    </div>

    {{-- Pending Alert --}}
    @if($pendingCount > 0)
    <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-2xl">
        <div class="flex items-center gap-3">
            <span class="text-xl">⚡</span>
            <div>
                <p class="text-sm font-black text-amber-800">{{ $pendingCount }} Pesanan Menunggu Konfirmasi</p>
                <p class="text-xs text-amber-600">Segera proses agar pembeli tidak menunggu lama.</p>
            </div>
        </div>
        <a href="{{ route('seller.orders') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white flex-shrink-0" style="background:#72bf77">Proses Sekarang</a>
    </div>
    @endif

    {{-- Store Schedule --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">⏰ Jadwal Toko</h2>
        <form method="POST" action="{{ route('seller.store-schedule') }}" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Jam Buka</label>
                <input type="time" name="open_time" value="{{ old('open_time', $seller->open_time) }}"
                    class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                @error('open_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Jam Tutup</label>
                <input type="time" name="close_time" value="{{ old('close_time', $seller->close_time) }}"
                    class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                @error('close_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                <input type="checkbox" name="auto_schedule" value="1" {{ old('auto_schedule', $seller->auto_schedule ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-[#72bf77]">
                Auto Schedule
            </label>
            <button type="submit" class="h-10 px-5 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">Simpan</button>
        </form>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pesanan Terbaru</h2>
            <a href="{{ route('seller.orders') }}" class="text-xs font-bold" style="color:#72bf77">Lihat Semua →</a>
        </div>
        @php
            $statusMap = ['pending'=>['Menunggu','bg-amber-100 text-amber-700'],'accepted'=>['Diproses','bg-blue-100 text-blue-700'],'done'=>['Selesai','bg-green-100 text-green-700'],'rejected'=>['Ditolak','bg-red-100 text-red-700'],'dibatalkan'=>['Dibatalkan','bg-gray-100 text-gray-500']];
        @endphp
        @forelse($recentOrders as $order)
        @php [$statusLabel,$statusClass] = $statusMap[$order->status] ?? [$order->status,'bg-gray-100 text-gray-600']; @endphp
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50/60 hover:bg-gray-50/40 transition">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0" style="background:rgba(114,191,119,.1);color:#3fa348">{{ strtoupper(substr($order->user->name??'?',0,1)) }}</div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ $order->user->name ?? 'Pembeli' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $order->product->name ?? 'Produk' }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 ml-3 flex-shrink-0">
                <p class="text-sm font-black text-gray-900 hidden sm:block">Rp {{ number_format($order->total_price,0,',','.') }}</p>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
                @if($order->status === 'pending')
                <form method="POST" action="/web/order/{{ $order->id }}/status" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white transition hover:opacity-80" style="background:#72bf77">Proses</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <span class="text-4xl mb-3">📭</span>
            <p class="font-bold text-gray-700">Belum ada pesanan</p>
            <p class="text-sm text-gray-400 mt-1">Pesanan dari pembeli akan muncul di sini.</p>
        </div>
        @endforelse
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('seller.products') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl text-center bg-white border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
            <span class="text-2xl group-hover:scale-110 transition">📦</span>
            <span class="text-xs font-black text-gray-700">Produk Saya</span>
            <span class="text-[10px] font-bold" style="color:#72bf77">{{ $productCount }} item</span>
        </a>
        <a href="{{ route('seller.orders') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl text-center bg-white border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
            <span class="text-2xl group-hover:scale-110 transition">🛒</span>
            <span class="text-xs font-black text-gray-700">Order Masuk</span>
            @if($pendingCount > 0)<span class="text-[10px] font-black text-amber-500">{{ $pendingCount }} pending</span>@endif
        </a>
        <a href="{{ route('seller.analytics') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl text-center bg-white border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
            <span class="text-2xl group-hover:scale-110 transition">📊</span>
            <span class="text-xs font-black text-gray-700">Analitik</span>
        </a>
        <a href="{{ route('seller.settings') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl text-center bg-white border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
            <span class="text-2xl group-hover:scale-110 transition">⚙️</span>
            <span class="text-xs font-black text-gray-700">Pengaturan</span>
        </a>
    </div>
</div>
@endsection
