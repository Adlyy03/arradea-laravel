@extends('layouts.dashboard')

@section('title', 'Temukan Produk - Arradea')
@section('page_title', 'Semua Produk')

@section('content')
<div class="space-y-6 lg:space-y-12">
        <form action="{{ route('buyer.products') }}" method="GET" class="bg-white rounded-2xl lg:rounded-3xl p-4 lg:p-6 border border-gray-100 shadow-sm">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <label for="product-search" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Cari Produk atau Kategori</label>
            <div class="flex gap-3">
                <input id="product-search" type="search" name="q" value="{{ $keyword ?? request('q') }}" placeholder="Contoh: sepatu, elektronik, aksesoris" class="flex-1 h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 font-bold text-sm text-gray-700 focus:border-primary-600 focus:outline-none" autocomplete="off">
                <button type="submit" class="px-5 h-12 bg-primary-600 text-white rounded-xl font-black text-sm hover:bg-primary-700 transition">Cari</button>
            </div>
        </form>

        <!-- Categories Filter -->
        <div class="flex overflow-x-auto gap-3 pb-6 mb-2 scrollbar-none">
            <a href="{{ route('buyer.products', array_filter(['q' => request('q')])) }}" class="whitespace-nowrap px-6 py-3 rounded-xl font-bold text-sm transition {{ !request('category') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-50 text-gray-600 hover:bg-primary-50 hover:text-primary-600' }}">Semua Kategori</a>
            @foreach($categories as $category)
                <a href="{{ route('buyer.products', array_filter(['category' => $category->slug, 'q' => request('q')])) }}" class="whitespace-nowrap px-6 py-3 rounded-xl font-bold text-sm transition {{ request('category') == $category->slug ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-50 text-gray-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

    <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            @forelse($products as $product)
                <div class="group bg-white rounded-2xl lg:rounded-[3rem] p-4 lg:p-6 border border-gray-100 shadow-sm hover:shadow-2xl transition" data-product-id="{{ $product->id }}">
                    <div class="aspect-square overflow-hidden rounded-xl lg:rounded-[2.2rem] mb-4">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/400?text=No+Image' }}" class="product-image w-full h-full object-cover" alt="{{ $product->name }}">
                    </div>
                    <div class="flex justify-between items-start mb-1">
                        <p class="product-category text-[9px] lg:text-[10px] font-black text-primary-500 uppercase tracking-widest">{{ $product->category->name ?? 'Umum' }}</p>
                    </div>
                    <p class="product-store text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $product->store->name ?? 'Arradea' }}</p>
                    <h3 class="product-name text-base lg:text-xl font-black text-gray-900 line-clamp-2 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-500 text-xs lg:text-sm line-clamp-2 mb-4">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="product-price text-lg lg:text-2xl font-black text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="product-stock text-[10px] lg:text-xs font-black uppercase tracking-widest {{ $product->stock > 5 ? 'text-green-600' : 'text-red-500' }}">{{ $product->stock }} stok</span>
                    </div>
                    <div class="mb-4">
                        <span class="product-status inline-flex px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $product->stock > 0 ? 'Tersedia' : 'Habis' }}</span>
                    </div>
                    <a href="/products/{{ $product->id }}" class="block text-center px-4 py-3 bg-primary-600 text-white rounded-xl font-black hover:bg-primary-700 transition lg:text-base text-sm">Lihat & Beli</a>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-12 lg:py-24 text-gray-400">
                    <h3 class="text-3xl font-black mb-4">Belum ada produk</h3>
                    <p>Tunggu seller menambahkan produk baru atau buat dulu toko Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $products->links() }}
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

        const updateProductCard = (payload) => {
            if (!payload || !payload.id) {
                return;
            }

            const card = document.querySelector('[data-product-id="' + payload.id + '"]');
            if (!card) {
                return;
            }

            const priceEl = card.querySelector('.product-price');
            const stockEl = card.querySelector('.product-stock');
            const statusEl = card.querySelector('.product-status');
            const nameEl = card.querySelector('.product-name');
            const storeEl = card.querySelector('.product-store');
            const categoryEl = card.querySelector('.product-category');
            const imageEl = card.querySelector('.product-image');

            const stock = Number(payload.stock || 0);

            if (priceEl) {
                priceEl.textContent = formatRupiah(payload.price);
            }

            if (stockEl) {
                stockEl.textContent = stock + ' stok';
                stockEl.classList.remove('text-green-600', 'text-red-500');
                stockEl.classList.add(stock > 5 ? 'text-green-600' : 'text-red-500');
            }

            if (statusEl) {
                statusEl.textContent = stock > 0 ? 'Tersedia' : 'Habis';
                statusEl.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                if (stock > 0) {
                    statusEl.classList.add('bg-green-100', 'text-green-700');
                } else {
                    statusEl.classList.add('bg-red-100', 'text-red-700');
                }
            }

            if (nameEl && payload.name) {
                nameEl.textContent = payload.name;
            }

            if (storeEl && payload.store_name) {
                storeEl.textContent = payload.store_name;
            }

            if (categoryEl && payload.category_name) {
                categoryEl.textContent = payload.category_name;
            }

            if (imageEl && payload.image) {
                imageEl.src = payload.image;
            }
        };

        const getVisibleProductIds = () => {
            return Array.from(document.querySelectorAll('[data-product-id]'))
                .map((card) => Number(card.getAttribute('data-product-id')))
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

            result.data.forEach((product) => updateProductCard(product));
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

            echoInstance.channel('products').listen('.ProductUpdated', updateProductCard);
        }

        pollProductUpdates();
        setInterval(() => {
            pollProductUpdates().catch(() => {});
        }, 5000);

        const searchInput = document.getElementById('product-search');
        if (searchInput && searchInput.form) {
            let debounceTimer = null;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchInput.form.submit();
                }, 450);
            });
        }
    })();
</script>
@endsection