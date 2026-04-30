@extends('layouts.dashboard')

@section('title', 'Order Masuk - Arradea Seller')
@section('page_title', 'Order Masuk')

@section('content')
<div class="space-y-5 fade-up">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-16 -left-8 w-48 h-48 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Manajemen Pesanan</p>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">Order <span style="color:#a3e4a6">Masuk</span> Toko</h1>
                <p class="text-white/50 text-sm mt-1.5">Konfirmasi dan kelola setiap pesanan dari pembeli.</p>
            </div>

            {{-- Quick Stats --}}
            <div class="flex gap-3 w-full lg:w-auto">
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.2)">
                    <p class="text-2xl font-black text-amber-400">{{ $pendingCount }}</p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-amber-300/70 mt-0.5">Pending</p>
                </div>
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.2)">
                    <p class="text-2xl font-black text-blue-400">{{ $orders->where('status','accepted')->count() }}</p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-blue-300/70 mt-0.5">Diproses</p>
                </div>
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2)">
                    <p class="text-2xl font-black text-green-400">{{ $doneCount }}</p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-green-300/70 mt-0.5">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Alert --}}
    @if($pendingCount > 0)
    <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-2xl">
        <div class="flex items-center gap-3">
            <span class="text-xl animate-bounce">⚡</span>
            <div>
                <p class="text-sm font-black text-amber-800">{{ $pendingCount }} pesanan menunggu konfirmasi Anda</p>
                <p class="text-xs text-amber-600">Respon cepat meningkatkan rating toko Anda.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        @php $activeStatus = request('status'); @endphp
        <a href="{{ route('seller.orders') }}"
           class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition {{ !$activeStatus ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
           style="{{ !$activeStatus ? 'background:#72bf77' : '' }}">Semua</a>
        @foreach(['pending'=>'⏳ Pending','accepted'=>'🔄 Diproses','done'=>'✅ Selesai','rejected'=>'❌ Ditolak'] as $key=>$label)
        <a href="{{ route('seller.orders', ['status'=>$key]) }}"
           class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeStatus===$key ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
           style="{{ $activeStatus===$key ? 'background:#72bf77' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">🛒 Daftar Pesanan</h2>
            <span class="text-xs font-bold text-gray-400">Total: {{ $orders->total() }}</span>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/80 text-[10px] font-black tracking-widest uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-4">Pembeli</th>
                        <th class="px-5 py-4">Produk</th>
                        <th class="px-5 py-4">Qty / Total</th>
                        <th class="px-5 py-4">Catatan</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    @php
                        $statusMap = [
                            'pending'    => ['Menunggu',  'bg-amber-100 text-amber-700'],
                            'accepted'   => ['Diproses',  'bg-blue-100 text-blue-700'],
                            'done'       => ['Selesai',   'bg-green-100 text-green-700'],
                            'rejected'   => ['Ditolak',   'bg-red-100 text-red-700'],
                            'dibatalkan' => ['Dibatalkan','bg-gray-100 text-gray-500'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500'];
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0"
                                     style="background:rgba(114,191,119,.1);color:#3fa348">
                                    {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm">{{ $order->user->name ?? 'Pembeli' }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($order->product?->image)
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                    <img src="{{ $order->product->image }}" alt="" class="w-full h-full object-cover">
                                </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $order->product->name ?? '—' }}</p>
                                    @php $variantName = data_get($order->product?->getVariant($order->variant_key), 'name', null); @endphp
                                    @if($variantName && $variantName !== 'Default')
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $variantName }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-black text-gray-900">{{ $order->quantity ?? 1 }}×</p>
                            @if(($order->discount_percent_applied ?? 0) > 0 && $order->unit_price_original)
                            <p class="text-[10px] text-gray-400 line-through">Rp {{ number_format($order->unit_price_original * $order->quantity, 0, ',', '.') }}</p>
                            @endif
                            <p class="text-sm font-black" style="color:#3fa348">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 max-w-[160px] line-clamp-2">{{ $order->notes ?: '—' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="{{ $statusClass }} px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($order->status === 'pending')
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-white transition hover:opacity-80 active:scale-95"
                                                style="background:#72bf77">✓ Terima</button>
                                    </form>
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-red-600 bg-red-100 hover:bg-red-200 transition active:scale-95">✕ Tolak</button>
                                    </form>
                                @elseif($order->status === 'accepted')
                                    <form action="/web/order/{{ $order->id }}/status" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-white bg-blue-600 hover:bg-blue-700 transition active:scale-95">✓ Selesai</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300 font-bold">—</span>
                                @endif
                                <a href="{{ route('chat.show', $order) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-black text-white bg-gray-700 hover:bg-gray-800 transition active:scale-95">
                                    💬 Chat
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-5xl">📭</span>
                                <p class="font-black text-gray-900 text-lg">Belum Ada Pesanan</p>
                                <p class="text-sm text-gray-400">Pesanan dari pembeli akan muncul di sini secara otomatis.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="lg:hidden divide-y divide-gray-50">
            @forelse($orders as $order)
            @php
                $statusMap = [
                    'pending'    => ['Menunggu',  'bg-amber-100 text-amber-700'],
                    'accepted'   => ['Diproses',  'bg-blue-100 text-blue-700'],
                    'done'       => ['Selesai',   'bg-green-100 text-green-700'],
                    'rejected'   => ['Ditolak',   'bg-red-100 text-red-700'],
                    'dibatalkan' => ['Dibatalkan','bg-gray-100 text-gray-500'],
                ];
                [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500'];
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black" style="background:rgba(114,191,119,.1);color:#3fa348">
                            {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm">{{ $order->user->name ?? 'Pembeli' }}</p>
                            <p class="text-[10px] font-bold text-gray-400">ARRD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <span class="{{ $statusClass }} px-2.5 py-1 rounded-lg text-[10px] font-black uppercase">{{ $statusLabel }}</span>
                </div>

                <div class="flex gap-3 p-3 bg-gray-50 rounded-xl">
                    @if($order->product?->image)
                    <img src="{{ $order->product->image }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $order->product->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">Qty: {{ $order->quantity }}× · <span class="font-black" style="color:#3fa348">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                @if($order->notes)
                <p class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">📝 {{ $order->notes }}</p>
                @endif

                <div class="flex gap-2 flex-wrap">
                    @if($order->status === 'pending')
                        <form action="/web/order/{{ $order->id }}/status" method="POST" class="flex-1">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-black text-white transition hover:opacity-80" style="background:#72bf77">✓ Terima Order</button>
                        </form>
                        <form action="/web/order/{{ $order->id }}/status" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="py-2 px-4 rounded-xl text-xs font-black text-red-600 bg-red-100 hover:bg-red-200 transition">Tolak</button>
                        </form>
                    @elseif($order->status === 'accepted')
                        <form action="/web/order/{{ $order->id }}/status" method="POST" class="flex-1">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="done">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 transition">✓ Tandai Selesai</button>
                        </form>
                    @endif
                    <a href="{{ route('chat.show', $order) }}" class="py-2 px-4 rounded-xl text-xs font-black text-white bg-gray-700 hover:bg-gray-800 transition">💬 Chat</a>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <span class="text-5xl">📭</span>
                <p class="font-black text-gray-900 mt-3">Belum Ada Pesanan</p>
                <p class="text-sm text-gray-400 mt-1">Pesanan dari pembeli akan muncul di sini.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
