@extends('layouts.dashboard')

@section('title', 'Manajemen Produk - Arradea')
@section('page_title', 'Produk Toko')

@section('content')
<div class="space-y-5 fade-up">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-32 opacity-5" style="background:#72bf77;filter:blur(80px)"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Inventori Toko</p>
                <h1 class="text-2xl lg:text-4xl font-black tracking-tight leading-tight">
                    Kelola <span style="color:#a3e4a6">Produk</span> Anda
                </h1>
                <p class="text-white/50 text-sm mt-1.5">Tambah, edit, dan pantau stok produk toko Anda secara real-time.</p>
            </div>
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <a href="/seller/products/create"
                   class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
                   style="background:#72bf77">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Produk Baru
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6" style="border-top:1px solid rgba(255,255,255,.08)">
            <div class="text-center text-white">
                <p class="text-2xl font-black" id="stat-total">{{ $products->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Total Produk</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-red-400" id="low-stock-count">{{ $products->where('stock', '<=', 5)->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Stok Menipis</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-amber-400">{{ $products->where('discount_percent', '>', 0)->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Produk Diskon</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-green-400">{{ $products->where('stock', '>', 0)->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Stok Tersedia</p>
            </div>
        </div>
    </div>

    {{-- Low Stock Alert --}}
    @if($products->where('stock', '<=', 5)->count() > 0)
    <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-2xl" id="low-stock-alert">
        <div class="flex items-center gap-3">
            <span class="text-xl animate-pulse">⚠️</span>
            <div>
                <p class="text-sm font-black text-red-800">{{ $products->where('stock', '<=', 5)->count() }} Produk dengan stok rendah</p>
                <p class="text-xs text-red-600">Segera lakukan restock agar pembeli tidak kecewa.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="productSearch" placeholder="Cari produk..." class="w-full h-10 bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 text-sm font-medium focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color:rgba(114,191,119,.4)">
        </div>
        <select id="stockFilter" class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
            <option value="all">Semua Stok</option>
            <option value="low">Stok Menipis (≤5)</option>
            <option value="ok">Stok Aman (>5)</option>
        </select>
        <select id="sortFilter" class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
            <option value="default">Urutan Default</option>
            <option value="price-asc">Harga: Terendah</option>
            <option value="price-desc">Harga: Tertinggi</option>
            <option value="stock-asc">Stok: Terendah</option>
        </select>
    </div>

    {{-- Product Table / Cards --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">📦 Katalog Produk</h2>
            <span class="text-xs font-bold text-gray-400" id="product-count-label">{{ $products->count() }} item</span>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left" id="products-table">
                <thead class="bg-gray-50/80 text-[10px] font-black tracking-widest uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-4">Detail Produk</th>
                        <th class="px-5 py-4">Harga</th>
                        <th class="px-5 py-4">Stok</th>
                        <th class="px-5 py-4">Diskon</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="products-tbody">
                    @forelse($products as $product)
                    <tr class="product-row hover:bg-gray-50/60 transition-all duration-200"
                        data-product-id="{{ $product->id }}"
                        data-name="{{ strtolower($product->name) }}"
                        data-stock="{{ $product->stock }}"
                        data-price="{{ $product->price }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm">
                                    <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200' }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-gray-900 leading-tight">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5">{{ Str::limit($product->description ?? 'Tidak ada deskripsi', 60) }}</p>
                                    <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-widest text-gray-400">ID-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="product-price font-black text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @if(($product->discount_percent ?? 0) > 0)
                            <p class="text-[10px] text-green-600 font-bold mt-0.5">Diskon {{ $product->discount_percent }}%</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="product-stock-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase
                                    {{ $product->stock > 5 ? 'bg-green-100 text-green-700' : ($product->stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->stock > 5 ? 'bg-green-500' : ($product->stock > 0 ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                    {{ $product->stock }} unit
                                </span>
                                <p class="product-status-note text-[9px] font-bold uppercase {{ $product->stock <= 5 ? 'text-red-400 animate-pulse' : 'text-green-500' }}">
                                    {{ $product->stock <= 5 ? ($product->stock == 0 ? 'Habis!' : 'Restock!') : 'Aman' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if(($product->discount_percent ?? 0) > 0)
                                <span class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-[10px] font-black">{{ $product->discount_percent }}% OFF</span>
                            @else
                                <span class="text-xs text-gray-300 font-bold">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="/seller/products/{{ $product->id }}/edit"
                                   class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-green-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                   title="Edit Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="/web/product/{{ $product->id }}" method="POST"
                                      onsubmit="return confirmSubmit(event, @js('Yakin hapus produk ini? Tindakan tidak bisa dibatalkan.'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                            title="Hapus Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-row">
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-5xl">📦</span>
                                <p class="font-black text-gray-900 text-lg">Belum Ada Produk</p>
                                <p class="text-sm text-gray-400">Mulailah dengan menambahkan produk pertama toko Anda.</p>
                                <a href="/seller/products/create"
                                   class="mt-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90"
                                   style="background:#72bf77">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="lg:hidden divide-y divide-gray-50" id="products-mobile">
            @forelse($products as $product)
            <div class="product-row p-4 flex gap-4 hover:bg-gray-50/50 transition"
                 data-product-id="{{ $product->id }}"
                 data-name="{{ strtolower($product->name) }}"
                 data-stock="{{ $product->stock }}"
                 data-price="{{ $product->price }}">
                <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                    <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200' }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-black text-gray-900 leading-tight text-sm">{{ $product->name }}</p>
                        <span class="product-stock-badge flex-shrink-0 px-2 py-0.5 rounded-lg text-[9px] font-black {{ $product->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $product->stock }}x</span>
                    </div>
                    <p class="product-price text-sm font-black text-gray-900 mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <a href="/seller/products/{{ $product->id }}/edit"
                           class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700 transition">Edit</a>
                        <form action="/web/product/{{ $product->id }}" method="POST"
                              onsubmit="return confirmSubmit(event, @js('Yakin hapus produk ini?'))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <span class="text-4xl">📦</span>
                <p class="font-black text-gray-900 mt-3">Belum Ada Produk</p>
                <a href="/seller/products/create" class="mt-3 inline-block px-5 py-2 rounded-xl text-sm font-bold text-white" style="background:#72bf77">Tambah</a>
            </div>
            @endforelse
        </div>

        {{-- No results state (hidden by default) --}}
        <div id="no-search-results" class="hidden px-10 py-12 text-center">
            <span class="text-4xl">🔍</span>
            <p class="font-black text-gray-900 mt-2">Produk tidak ditemukan</p>
            <p class="text-sm text-gray-400 mt-1">Coba kata kunci yang berbeda.</p>
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

    // ── Search & Filter ──────────────────────────────
    const searchInput = document.getElementById('productSearch');
    const stockFilter = document.getElementById('stockFilter');
    const sortFilter = document.getElementById('sortFilter');
    const noResults = document.getElementById('no-search-results');
    const countLabel = document.getElementById('product-count-label');

    function filterAndSort() {
        const query = searchInput.value.toLowerCase().trim();
        const stockVal = stockFilter.value;
        const sortVal = sortFilter.value;

        let rows = Array.from(document.querySelectorAll('.product-row'));
        let visible = 0;

        rows.forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const stock = Number(row.dataset.stock || 0);
            const price = Number(row.dataset.price || 0);

            const matchName = !query || name.includes(query);
            const matchStock = stockVal === 'all' || (stockVal === 'low' && stock <= 5) || (stockVal === 'ok' && stock > 5);

            if (matchName && matchStock) {
                row.classList.remove('hidden');
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Sort
        const tbody = document.getElementById('products-tbody');
        if (tbody && sortVal !== 'default') {
            const tRows = Array.from(tbody.querySelectorAll('.product-row'));
            tRows.sort((a, b) => {
                if (sortVal === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sortVal === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
                if (sortVal === 'stock-asc') return Number(a.dataset.stock) - Number(b.dataset.stock);
                return 0;
            });
            tRows.forEach(r => tbody.appendChild(r));
        }

        if (noResults) noResults.classList.toggle('hidden', visible > 0);
        if (countLabel) countLabel.textContent = visible + ' item';
    }

    if (searchInput) searchInput.addEventListener('input', filterAndSort);
    if (stockFilter) stockFilter.addEventListener('change', filterAndSort);
    if (sortFilter) sortFilter.addEventListener('change', filterAndSort);

    // ── Real-time Updates ────────────────────────────
    const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

    const refreshLowStockCounter = () => {
        const lowStockEl = document.getElementById('low-stock-count');
        if (!lowStockEl) return;
        const rows = document.querySelectorAll('.product-row');
        let count = 0;
        rows.forEach(row => {
            if (Number(row.dataset.stock || 0) <= 5) count++;
        });
        lowStockEl.textContent = count;
    };

    const updateProductRow = (payload) => {
        if (!payload || !payload.id) return;
        const row = document.querySelector('[data-product-id="' + payload.id + '"]');
        if (!row) return;

        const stock = Number(payload.stock || 0);
        row.dataset.stock = stock;
        row.dataset.price = payload.price || row.dataset.price;

        const priceEl = row.querySelector('.product-price');
        const stockBadgeEl = row.querySelector('.product-stock-badge');
        const statusNoteEl = row.querySelector('.product-status-note');

        if (priceEl) priceEl.textContent = formatRupiah(payload.price);

        if (stockBadgeEl) {
            stockBadgeEl.textContent = stock + ' unit';
            stockBadgeEl.className = stockBadgeEl.className.replace(/bg-\w+-\d+ text-\w+-\d+/g, '').trim();
            if (stock > 5) stockBadgeEl.classList.add('bg-green-100', 'text-green-700');
            else if (stock > 0) stockBadgeEl.classList.add('bg-amber-100', 'text-amber-700');
            else stockBadgeEl.classList.add('bg-red-100', 'text-red-700');
        }

        if (statusNoteEl) {
            statusNoteEl.className = statusNoteEl.className.replace(/text-\w+-\d+|animate-pulse/g, '').trim();
            if (stock <= 5) {
                statusNoteEl.textContent = stock === 0 ? 'Habis!' : 'Restock!';
                statusNoteEl.classList.add('text-red-400', 'animate-pulse');
            } else {
                statusNoteEl.textContent = 'Aman';
                statusNoteEl.classList.add('text-green-500');
            }
        }

        // Pulse animation on update
        row.style.background = 'rgba(114,191,119,.08)';
        setTimeout(() => { row.style.background = ''; }, 1200);

        refreshLowStockCounter();
    };

    const getVisibleProductIds = () =>
        Array.from(document.querySelectorAll('.product-row'))
            .map(r => Number(r.getAttribute('data-product-id')))
            .filter(id => Number.isInteger(id));

    let lastSyncAt = null;
    const pollProductUpdates = async () => {
        const ids = getVisibleProductIds();
        if (!ids.length) return;
        const params = new URLSearchParams();
        ids.forEach(id => params.append('ids[]', String(id)));
        if (lastSyncAt) params.append('since', lastSyncAt);
        try {
            const response = await fetch('/api/products/updates?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                credentials: 'same-origin',
            });
            if (!response.ok) return;
            const result = await response.json();
            if (!result.success || !Array.isArray(result.data)) return;
            result.data.forEach(p => updateProductRow(p));
            lastSyncAt = new Date().toISOString();
        } catch (_) {}
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
            auth: { headers: { 'X-CSRF-TOKEN': csrfToken } },
        });
        echoInstance.channel('products').listen('.ProductUpdated', updateProductRow);
    }

    pollProductUpdates();
    setInterval(() => { pollProductUpdates().catch(() => {}); }, 5000);
})();
</script>
@endsection
