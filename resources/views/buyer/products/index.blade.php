@extends('layouts.dashboard')
@section('title', 'Temukan Produk — Arradea')
@section('page_title', 'Semua Produk')

@push('styles')
<style>
    /* Force 3 columns on mobile */
    .product-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    
    @media(min-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        }
    }
    
    /* Mobile product grid - 3 columns compact */
    @media(max-width:1023px){
        .product-grid { gap: 8px !important; }
        .product-card { border-radius: 10px !important; }
        .product-image-wrapper { 
            aspect-ratio: 1 / 1 !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        .product-image { 
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
        }
        .product-content { padding: 7px !important; }
        .product-category { display: none !important; }
        .product-store { font-size: 9px !important; margin-bottom: 3px !important; }
        .product-name { font-size: 11px !important; line-height: 1.3 !important; margin-bottom: 4px !important; -webkit-line-clamp: 2 !important; }
        .product-price { font-size: 11px !important; }
        .product-old-price { font-size: 9px !important; }
        .product-stock { display: none !important; }
        .product-btn { padding: 7px 10px !important; font-size: 10px !important; border-radius: 7px !important; }
        .discount-badge { top: 4px !important; left: 4px !important; padding: 2px 5px !important; font-size: 8px !important; border-radius: 5px !important; }
        .search-bar { padding: 12px !important; border-radius: 14px !important; }
        .search-bar input { height: 38px !important; font-size: 13px !important; border-radius: 11px !important; padding-left: 12px !important; }
        .search-bar button { height: 38px !important; padding: 0 14px !important; font-size: 12px !important; border-radius: 11px !important; }
        .category-pills { gap: 6px !important; padding-bottom: 5px !important; }
        .category-pill { padding: 12px 12px !important; font-size: 11px !important; border-radius: 8px !important; white-space: nowrap !important; line-height: 1.3 !important; }
        .results-header { font-size: 12px !important; }
        .price-row { flex-direction: column !important; align-items: flex-start !important; gap: 2px !important; }
    }
    
    @media(max-width:375px){
        .product-name { font-size: 10px !important; }
        .product-price { font-size: 10px !important; }
        .product-btn { font-size: 9px !important; padding: 6px 8px !important; }
    }
</style>
@endpush

@section('content')
<div data-aos="fade-up" class="space-y-5">

    {{-- Search & Filter --}}
    <div data-aos="fade-down" data-aos-delay="100" class="search-bar bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4">
        <form action="{{ route('buyer.products') }}" method="GET">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input id="product-search" type="search" name="q" value="{{ $keyword ?? request('q') }}"
                        placeholder="Cari produk..."
                        class="w-full h-9 lg:h-10 bg-gray-50 border border-gray-200 rounded-lg lg:rounded-xl pl-3 lg:pl-4 pr-3 lg:pr-4 text-xs lg:text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                </div>
                <button type="submit" class="h-9 lg:h-10 px-4 lg:px-5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">Cari</button>
            </div>
        </form>
    </div>

    {{-- Category Pills --}}
    <div data-aos="fade-right" data-aos-delay="150" class="category-pills flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('buyer.products', array_filter(['q'=>request('q')])) }}"
            class="category-pill flex-shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold transition {{ !request('category') ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
            style="{{ !request('category') ? 'background:#72bf77' : '' }}">Semua</a>
        @foreach($categories as $cat)
        <a href="{{ route('buyer.products', array_filter(['category'=>$cat->slug,'q'=>request('q')])) }}"
            class="category-pill flex-shrink-0 px-4 py-1.5 rounded-lg text-xs font-bold transition {{ request('category')===$cat->slug ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300' }}"
            style="{{ request('category')===$cat->slug ? 'background:#72bf77' : '' }}">{{ $cat->name }}</a>
        @endforeach
    </div>

    {{-- Results header --}}
    <div class="results-header flex items-center justify-between">
        <p class="text-xs lg:text-sm text-gray-500">
            <span class="font-bold text-gray-900">{{ $products->total() }}</span> produk
            @if(request('q'))<span> untuk "<em class="text-gray-700">{{ request('q') }}</em>"</span>@endif
        </p>
    </div>

    {{-- Product Grid: 3 cols mobile, 6 cols desktop --}}
    <div class="product-grid grid grid-cols-3 lg:grid-cols-6 gap-2 lg:gap-4">
        @forelse($products as $product)
        @php
            $isDiscount = $product->discount_percent > 0;
            $finalPrice = $isDiscount ? $product->price * (1 - $product->discount_percent/100) : $product->price;
        @endphp
        <div data-aos="fade-up" data-aos-delay="{{ ($loop->index % 6) * 50 }}" class="product-card group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-green-100/40 hover:border-green-200/50 transition-all duration-300" data-product-id="{{ $product->id }}">
            <div class="product-image-wrapper relative w-full aspect-square overflow-hidden bg-gray-50">
                <img src="{{ $product->image ?? 'https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk' }}"
                    alt="{{ $product->name }}"
                    class="product-image w-full h-full object-cover object-center block transition-transform duration-500 group-hover:scale-105"
                    onerror="this.src='https://via.placeholder.com/400x400/f0faf1/72bf77?text=Produk'">
                @if($isDiscount)
                    <span class="discount-badge absolute top-2 left-2 px-1.5 py-0.5 rounded-lg text-[9px] font-black text-white" style="background:#72bf77">-{{ $product->discount_percent }}%</span>
                @endif
                @if($product->stock === 0)
                    <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                        <span class="px-2 py-1 bg-gray-800 text-white text-[9px] font-black rounded-lg">Habis</span>
                    </div>
                @endif
            </div>
            <div class="product-content p-2 lg:p-3">
                <p class="product-category text-[8px] font-black uppercase tracking-wider text-gray-400 mb-0.5 hidden lg:block">{{ $product->category->name ?? 'Umum' }}</p>
                <p class="product-store text-[8px] lg:text-[10px] font-bold truncate mb-1" style="color:#72bf77">🏪 {{ $product->store->name ?? 'Arradea' }}</p>
                <h3 class="product-name font-black text-gray-900 line-clamp-2 text-[10px] lg:text-xs leading-snug mb-1.5">{{ $product->name }}</h3>
                <div class="price-row flex items-end justify-between mb-1.5 lg:mb-2">
                    <div>
                        @if($isDiscount)
                            <p class="product-old-price text-[8px] lg:text-[9px] text-gray-400 line-through">Rp {{ number_format($product->price,0,',','.') }}</p>
                            <p class="product-price font-black text-[10px] lg:text-sm leading-none" style="color:#72bf77">Rp {{ number_format($finalPrice,0,',','.') }}</p>
                        @else
                            <p class="product-price font-black text-[10px] lg:text-sm leading-none text-gray-900">Rp {{ number_format($product->price,0,',','.') }}</p>
                        @endif
                    </div>
                    <span class="product-stock text-[8px] lg:text-[9px] font-bold {{ $product->stock > 5 ? 'text-green-600' : 'text-amber-500' }} hidden lg:block">Stok {{ $product->stock }}</span>
                </div>
                <a href="{{ route('buyer.products.show', $product->id) }}"
                    class="product-btn block w-full py-1.5 lg:py-2 rounded-lg text-[9px] lg:text-[10px] font-black text-white text-center transition hover:opacity-90 {{ $product->stock === 0 ? 'opacity-50 pointer-events-none' : '' }}"
                    style="background:#72bf77">
                    {{ $product->stock > 0 ? 'Beli' : 'Habis' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 lg:py-20 text-center">
            <span class="text-3xl lg:text-5xl mb-3 lg:mb-4">🔍</span>
            <p class="text-base lg:text-xl font-black text-gray-900 mb-1 lg:mb-2">Produk tidak ditemukan</p>
            <p class="text-xs lg:text-sm text-gray-400 mb-3 lg:mb-5">Coba kata kunci lain</p>
            <a href="{{ route('buyer.products') }}" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-white" style="background:#72bf77">Reset</a>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4">{{ $products->links() }}</div>
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