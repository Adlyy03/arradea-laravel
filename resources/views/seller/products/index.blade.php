@extends('layouts.dashboard')

@section('title', 'Manajemen Produk - Arradea')
@section('page_title', 'Daftar Produk Toko')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-6 lg:p-12 rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Kelola <span class="text-primary-600">Produk</span> Anda.</h1>
            <p class="text-gray-500 font-medium leading-relaxed">Tambahkan, edit, atau hapus produk jualan Anda dengan mudah. Pastikan stok dan deskripsi produk selalu diperbarui.</p>
        </div>
        <div class="flex-shrink-0 w-full lg:w-auto">
            <a href="/seller/products/create" class="w-full lg:w-auto px-8 lg:px-5 lg:px-10 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl shadow-primary-200 hover:scale-105 active:scale-95 transition-all flex items-center justify-center hover:bg-primary-700">
                + Produk Baru
            </a>
        </div>
    </div>

    <!-- Stats Row for Products -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        <div class="bg-primary-900 p-6 lg:p-8 rounded-2xl lg:rounded-[2.5rem] shadow-xl text-white">
            <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1 lg:mb-2">Total</p>
            <h3 class="text-2xl lg:text-4xl font-black">{{ $products->count() }} Item</h3>
        </div>
        <div class="bg-white p-6 lg:p-8 rounded-2xl lg:rounded-[2.5rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 lg:mb-2">Stok Menipis</p>
            <h3 id="low-stock-count" class="text-2xl lg:text-4xl font-black text-accent">{{ $products->where('stock', '<=', 5)->count() }} Item</h3>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 text-[10px] font-black tracking-widest uppercase text-gray-400">
                <tr>
                    <th class="px-5 lg:px-10 py-8">Detail Produk</th>
                    <th class="px-5 lg:px-10 py-8">Kategori / Kode</th>
                    <th class="px-5 lg:px-10 py-8">Harga Jual</th>
                    <th class="px-5 lg:px-10 py-8">Status Stok</th>
                    <th class="px-5 lg:px-10 py-8">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-primary-50/30 transition duration-300" data-product-id="{{ $product->id }}">
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-[1.5rem] bg-gray-100 overflow-hidden flex-shrink-0 shadow-inner border border-gray-100">
                                    <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200' }}" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                                </div>
                                <div class="space-y-1">
                                    <p class="font-black text-xl text-gray-900 leading-tight">{{ $product->name }}</p>
                                    <p class="text-sm font-medium text-gray-400 line-clamp-1">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex flex-col gap-1">
                                <span class="px-4 py-2 bg-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 w-max">Lifestyle</span>
                                <span class="text-xs font-bold text-gray-300 tracking-widest uppercase">ID-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <p class="product-price text-2xl font-black text-gray-900 tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex flex-col gap-2">
                                <span class="product-stock-badge {{ $product->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest w-max">
                                    {{ $product->stock }} Tersedia
                                </span>
                                <p class="product-status-note text-[10px] font-bold uppercase {{ $product->stock <= 5 ? 'text-red-400 animate-pulse' : 'text-green-500' }}">{{ $product->stock <= 5 ? 'Segera Restock!' : 'Stok Aman' }}</p>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex items-center gap-4">
                                <a href="/seller/products/{{ $product->id }}/edit" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-primary-600 hover:text-white hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="/web/product/{{ $product->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-10 lg:px-20 py-16 lg:py-32 text-center text-gray-400 font-bold space-y-4">
                            <div class="text-4xl lg:text-6xl mb-6">📦</div>
                            <p class="text-2xl text-gray-900 font-black">Belum Ada Produk.</p>
                            <p class="text-sm font-medium">Mulailah dengan menambahkan produk pertama toko Anda.</p>
                            <div class="pt-8">
                                <a href="/seller/products/create" class="px-5 lg:px-10 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-3xl font-black text-lg">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    (function () {
        const csrfToken = @json(csrf_token());
        const broadcastKey = @json(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')));
        const broadcastCluster = @json(env('PUSHER_APP_CLUSTER', 'mt1'));
        const broadcastHost = @json(env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1')));
        const broadcastPort = Number(@json((int) env('REVERB_PORT', env('PUSHER_PORT', 8080))));
        const broadcastScheme = @json(env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http')));

        const formatRupiah = (value) => {
            const number = Number(value || 0);
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        };

        const refreshLowStockCounter = () => {
            const lowStockCountEl = document.getElementById('low-stock-count');
            if (!lowStockCountEl) {
                return;
            }

            const rows = document.querySelectorAll('tr[data-product-id]');
            let lowStockCount = 0;

            rows.forEach((row) => {
                const badge = row.querySelector('.product-stock-badge');
                if (!badge) {
                    return;
                }

                const stockText = badge.textContent || '';
                const stock = Number((stockText.match(/\d+/) || [0])[0]);
                if (stock <= 5) {
                    lowStockCount += 1;
                }
            });

            lowStockCountEl.textContent = lowStockCount + ' Item';
        };

        const updateProductRow = (payload) => {
            if (!payload || !payload.id) {
                return;
            }

            const row = document.querySelector('tr[data-product-id="' + payload.id + '"]');
            if (!row) {
                return;
            }

            const priceEl = row.querySelector('.product-price');
            const stockBadgeEl = row.querySelector('.product-stock-badge');
            const statusNoteEl = row.querySelector('.product-status-note');

            const stock = Number(payload.stock || 0);

            if (priceEl) {
                priceEl.textContent = formatRupiah(payload.price);
            }

            if (stockBadgeEl) {
                stockBadgeEl.textContent = stock + ' Tersedia';
                stockBadgeEl.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                if (stock > 5) {
                    stockBadgeEl.classList.add('bg-green-100', 'text-green-700');
                } else {
                    stockBadgeEl.classList.add('bg-red-100', 'text-red-700');
                }
            }

            if (statusNoteEl) {
                statusNoteEl.classList.remove('text-red-400', 'animate-pulse', 'text-green-500');
                if (stock <= 5) {
                    statusNoteEl.textContent = 'Segera Restock!';
                    statusNoteEl.classList.add('text-red-400', 'animate-pulse');
                } else {
                    statusNoteEl.textContent = 'Stok Aman';
                    statusNoteEl.classList.add('text-green-500');
                }
            }

            refreshLowStockCounter();
        };

        const getVisibleProductIds = () => {
            return Array.from(document.querySelectorAll('tr[data-product-id]'))
                .map((row) => Number(row.getAttribute('data-product-id')))
                .filter((id) => Number.isInteger(id));
        };

        let lastSyncAt = null;
        const pollProductUpdates = async () => {
            const ids = getVisibleProductIds();
            if (!ids.length) {
                return;
            }

            const params = new URLSearchParams();
            ids.forEach((id) => params.append('ids[]', String(id)));
            if (lastSyncAt) {
                params.append('since', lastSyncAt);
            }

            const response = await fetch('/api/products/updates?' + params.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const result = await response.json();
            if (!result.success || !Array.isArray(result.data)) {
                return;
            }

            result.data.forEach((product) => updateProductRow(product));
            lastSyncAt = new Date().toISOString();
        };

        if (broadcastKey && typeof window.Pusher !== 'undefined' && typeof window.Echo !== 'undefined') {
            const echoInstance = new window.Echo({
                broadcaster: 'pusher',
                key: broadcastKey,
                cluster: broadcastCluster,
                wsHost: broadcastHost,
                wsPort: broadcastPort,
                wssPort: broadcastPort,
                forceTLS: broadcastScheme === 'https',
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                },
            });

            echoInstance.channel('products').listen('.ProductUpdated', updateProductRow);
        }

        pollProductUpdates();
        setInterval(() => {
            pollProductUpdates().catch(() => {});
        }, 5000);
    })();
</script>
@endsection
