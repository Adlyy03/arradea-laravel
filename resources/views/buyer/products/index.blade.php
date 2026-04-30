@extends('layouts.dashboard')
@section('title', 'Temukan Produk — Arradea')
@section('page_title', 'Semua Produk')

@section('content')
<div class="space-y-5 fade-up">

    {{-- Search & Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <form action="{{ route('buyer.products') }}" method="GET">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="product-search" type="search" name="q" value="{{ $keyword ?? request('q') }}"
                        placeholder="Cari produk, toko, kategori..."
                        class="w-full h-10 bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                </div>
                <button type="submit" class="h-10 px-5 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">Cari</button>
            </div>
        </form>
    </div>

    {{-- Category Pills --}}
    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('buyer.products', array_filter(['q'=>request('q')])) }}"
            class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('category') ? 'text-white shadow-md' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
            style="{{ !request('category') ? 'background:#72bf77' : '' }}">Semua</a>
        @foreach($categories as $cat)
        <a href="{{ route('buyer.products', array_filter(['category'=>$cat->slug,'q'=>request('q')])) }}"
            class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category')===$cat->slug ? 'text-white shadow-md' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
            style="{{ request('category')===$cat->slug ? 'background:#72bf77' : '' }}">{{ $cat->name }}</a>
        @endforeach
    </div>

    {{-- Results header --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            <span class="font-bold text-gray-900">{{ $products->total() }}</span> produk ditemukan
            @if(request('q'))<span> untuk "<em class="text-gray-700">{{ request('q') }}</em>"</span>@endif
        </p>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($products as $product)
        @php
            $isDiscount = $product->discount_percent > 0;
            $finalPrice = $isDiscount ? $product->price * (1 - $product->discount_percent/100) : $product->price;
        @endphp
        <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-green-100/40 hover:border-green-200/50 transition-all duration-300" data-product-id="{{ $product->id }}">
            <div class="relative aspect-square overflow-hidden bg-gray-50">
                <img src="{{ $product->image ?? 'https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk' }}"
                    alt="{{ $product->name }}"
                    class="product-image w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.src='https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk'">
                @if($isDiscount)
                    <span class="absolute top-3 left-3 px-2 py-0.5 rounded-lg text-[10px] font-black text-white" style="background:#72bf77">-{{ $product->discount_percent }}%</span>
                @endif
                @if($product->stock === 0)
                    <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                        <span class="px-3 py-1.5 bg-gray-800 text-white text-xs font-black rounded-xl">Habis Terjual</span>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-0.5 product-category">{{ $product->category->name ?? 'Umum' }}</p>
                <p class="text-[10px] font-bold truncate mb-1 product-store" style="color:#72bf77">🏪 {{ $product->store->name ?? 'Arradea' }}</p>
                <h3 class="font-black text-gray-900 line-clamp-2 text-sm leading-snug mb-2 product-name">{{ $product->name }}</h3>
                <div class="flex items-end justify-between mb-3">
                    <div>
                        @if($isDiscount)
                            <p class="text-[10px] text-gray-400 line-through">Rp {{ number_format($product->price,0,',','.') }}</p>
                            <p class="font-black text-base leading-none product-price" style="color:#72bf77">Rp {{ number_format($finalPrice,0,',','.') }}</p>
                        @else
                            <p class="font-black text-base leading-none product-price text-gray-900">Rp {{ number_format($product->price,0,',','.') }}</p>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold product-stock {{ $product->stock > 5 ? 'text-green-600' : 'text-amber-500' }}">Stok {{ $product->stock }}</span>
                </div>
                <a href="{{ route('buyer.products.show', $product->id) }}"
                    class="block w-full py-2.5 rounded-xl text-xs font-black text-white text-center transition hover:opacity-90 {{ $product->stock === 0 ? 'opacity-50 pointer-events-none' : '' }}"
                    style="background:#72bf77">
                    {{ $product->stock > 0 ? 'Lihat & Beli' : 'Habis' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <span class="text-5xl mb-4">🔍</span>
            <p class="text-xl font-black text-gray-900 mb-2">Produk tidak ditemukan</p>
            <p class="text-sm text-gray-400 mb-5">Coba kata kunci yang berbeda atau hapus filter kategori</p>
            <a href="{{ route('buyer.products') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#72bf77">Reset Pencarian</a>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="bg-white rounded-2xl border border-gray-100 p-4">{{ $products->links() }}</div>
    @endif
</div>

{{-- Keep existing real-time JS intact --}}
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function(){
    const csrfToken = @json(csrf_token());
    const broadcastKey = @json(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')));
    const broadcastCluster = @json(env('PUSHER_APP_CLUSTER','mt1'));
    const broadcastHost = @json(env('REVERB_HOST', env('PUSHER_HOST','127.0.0.1')));
    const broadcastPort = Number(@json((int) env('REVERB_PORT', env('PUSHER_PORT',8080))));
    const broadcastScheme = @json(env('REVERB_SCHEME', env('PUSHER_SCHEME','http')));
    const formatRupiah = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v||0));
    const updateCard = p => {
        if(!p||!p.id) return;
        const card = document.querySelector('[data-product-id="'+p.id+'"]');
        if(!card) return;
        const s = Number(p.stock||0);
        card.querySelector('.product-price')?.textContent && (card.querySelector('.product-price').textContent = formatRupiah(p.price));
        const stockEl = card.querySelector('.product-stock');
        if(stockEl){ stockEl.textContent='Stok '+s; stockEl.className='text-[10px] font-bold product-stock '+(s>5?'text-green-600':'text-amber-500'); }
    };
    let lastSyncAt = null;
    const poll = async () => {
        const ids = Array.from(document.querySelectorAll('[data-product-id]')).map(c=>Number(c.dataset.productId)).filter(n=>Number.isInteger(n));
        if(!ids.length) return;
        const p = new URLSearchParams(); ids.forEach(id=>p.append('ids[]',id)); if(lastSyncAt) p.append('since',lastSyncAt);
        const r = await fetch('/api/products/updates?'+p.toString(),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrfToken},credentials:'same-origin'}).catch(()=>null);
        if(!r?.ok) return;
        const d = await r.json();
        if(d.success && Array.isArray(d.data)){ d.data.forEach(updateCard); lastSyncAt = new Date().toISOString(); }
    };
    if(broadcastKey && typeof window.Echo!=='undefined'){
        new window.Echo({broadcaster:'pusher',key:broadcastKey,cluster:broadcastCluster,wsHost:broadcastHost,wsPort:broadcastPort,wssPort:broadcastPort,forceTLS:broadcastScheme==='https',enabledTransports:['ws','wss']}).channel('products').listen('.ProductUpdated',updateCard);
    }
    poll(); setInterval(()=>poll().catch(()=>{}),5000);
    const si = document.getElementById('product-search');
    if(si?.form){ let t; si.addEventListener('input',()=>{ clearTimeout(t); t=setTimeout(()=>si.form.submit(),450); }); }
})();
</script>
@endsection