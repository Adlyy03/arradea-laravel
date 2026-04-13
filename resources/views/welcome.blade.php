@extends('layouts.app')

@section('content')
<!-- ARRADEA_RENDER_MARKER: Jika Anda melihat ini di source code, berarti file benar -->
<div class="h-full">
    <!-- Hero Section -->
    <header class="relative px-6 sm:px-8 py-10 lg:py-32 overflow-hidden bg-white">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-20">
            <div class="flex-1 space-y-6 lg:space-y-10 z-10 animate-fade-in text-center lg:text-left">
                <div class="inline-flex items-center px-4 py-2 bg-primary-100/50 rounded-full text-primary-700 text-[10px] font-black tracking-[0.2em] uppercase">
                    🏘️ Pasar Warga Arradea
                </div>
                <h1 class="text-3xl lg:text-5xl md:text-7xl lg:text-9xl font-black text-gray-900 tracking-tighter leading-[0.85] lg:leading-[0.75] mb-6">
                    Segar<br><span class="text-primary-600 underline underline-offset-[1.5rem] lg:underline-offset-[2rem] decoration-primary-200">Dekat</span><br>Lengkap.
                </h1>
                <p class="text-xl lg:text-2xl text-gray-400 max-w-xl font-medium leading-relaxed">
                    Dukung jualan tetangga! Dari makanan ibu-ibu komplek sampai jasa profesional, semua ada di sini. Belanja lebih dekat, lebih akrab.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 lg:gap-6 pt-6">
                    <a href="#feed" class="px-6 lg:px-14 py-4 lg:py-6 bg-primary-600 text-white rounded-2xl lg:rounded-[2.5rem] font-black text-lg lg:text-2xl shadow-xl lg:shadow-3xl shadow-primary-200 hover:scale-[1.05] hover:bg-primary-700 active:scale-95 transition-all">
                        Belanja Sekarang
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-6 lg:px-14 py-4 lg:py-6 bg-gray-50 text-gray-800 rounded-2xl lg:rounded-[2.5rem] font-black text-lg lg:text-2xl hover:bg-gray-100 transition-all border border-gray-100 shadow-sm">
                            Gabung Seller
                        </a>
                    @else
                        <a href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('buyer.dashboard') }}" class="px-6 lg:px-14 py-4 lg:py-6 bg-primary-900 text-white rounded-2xl lg:rounded-[2.5rem] font-black text-lg lg:text-2xl hover:bg-black transition-all shadow-xl">
                            Akses Dashboard
                        </a>
                    @endguest
                </div>
            </div>
            
            <!-- Floating Image Decor -->
            <div class="flex-1 relative hidden lg:block">
                <div class="absolute -top-32 -right-32 w-[600px] h-[600px] bg-primary-100 rounded-full mix-blend-multiply blur-[120px] opacity-40"></div>
                <div class="relative overflow-hidden rounded-[5rem] shadow-4xl border-[15px] border-white transform hover:rotate-2 transition-transform duration-1000 group">
                    <img src="file:///C:/Users/hp/.gemini/antigravity/brain/aa688169-6c1a-47ea-b17e-3440e1e9f921/neighborhood_marketplace_hero_1775208375523.png" alt="Hero" class="w-full h-[700px] object-cover group-hover:scale-110 transition duration-[2s]">
                </div>
            </div>
        </div>
    </header>

    <!-- Featured Categories -->
    <section class="py-12 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-5xl font-black tracking-tighter mb-4">Kategori <span class="text-primary-600">Populer</span>.</h2>
                <p class="text-gray-400 text-lg font-medium">Temukan produk berdasarkan kategori favorit Anda.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-8">
                @php $featuredCategories = \App\Models\Category::featured()->parents()->orderBy('sort_order')->get(); @endphp
                @forelse ($featuredCategories as $category)
                    <a href="/categories/{{ $category->slug }}" class="group bg-white rounded-2xl lg:rounded-3xl p-6 lg:p-8 hover:shadow-xl transition-all duration-300 text-center border border-gray-100 hover:border-primary-200">
                        <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto mb-4 lg:mb-6 bg-primary-50 rounded-2xl flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                            <span class="text-2xl lg:text-3xl">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        <h3 class="text-sm lg:text-base font-black text-gray-900 group-hover:text-primary-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-[10px] lg:text-xs text-gray-400 font-medium mt-1">{{ $category->getProductsCount() }} produk</p>
                    </a>
                @empty
                    <div class="col-span-full text-center py-6 lg:py-12">
                        <p class="text-gray-400 font-medium">Kategori sedang dimuat...</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Stats Stats Stats -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 -mt-12 lg:-mt-20 relative z-20">
        <div class="bg-primary-900 rounded-3xl lg:rounded-[4rem] p-8 lg:p-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-white shadow-3xl">
            <div class="text-center md:text-left space-y-1 lg:space-y-2">
                <h3 class="text-4xl lg:text-5xl font-black italic tracking-tighter">500+</h3>
                <p class="text-[10px] lg:text-sm font-black uppercase tracking-widest text-primary-300">Transaksi Warga</p>
            </div>
            <div class="text-center md:text-left space-y-1 lg:space-y-2 border-y md:border-y-0 md:border-x border-white/10 py-6 md:py-0 md:px-6 lg:px-12">
                <h3 class="text-4xl lg:text-5xl font-black italic tracking-tighter">100+</h3>
                <p class="text-[10px] lg:text-sm font-black uppercase tracking-widest text-primary-300">Tetangga Berjualan</p>
            </div>
            <div class="text-center md:text-left space-y-1 lg:space-y-2">
                <h3 class="text-4xl lg:text-5xl font-black italic tracking-tighter">Terpercaya</h3>
                <p class="text-[10px] lg:text-sm font-black uppercase tracking-widest text-primary-300">Kepuasan Tetangga</p>
            </div>
        </div>
    </section>

    <!-- Main Feed from DB -->
    <section id="feed" class="py-16 lg:py-32">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <div class="flex justify-between items-end mb-12 lg:mb-20 text-center md:text-left">
                <div>
                    <h2 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight mb-4 lg:mb-6">Jualan <span class="text-primary-600">Tetangga</span>.</h2>
                    <p class="text-gray-400 text-lg lg:text-xl font-medium">Temukan produk unik dan jasa terbaik langsung dari warga di sekitarmu.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-12">
                @php $products = \App\Models\Product::with('store')->latest()->take(12)->get(); @endphp
                @forelse ($products as $product)
                    <div class="group bg-white rounded-3xl lg:rounded-[4rem] p-6 lg:p-8 shadow-sm hover:shadow-2xl transition duration-500 border border-gray-100 flex flex-col h-full overflow-hidden">
                        <div class="relative aspect-square overflow-hidden rounded-2xl lg:rounded-[3rem] mb-6 lg:mb-10 shrink-0 shadow-inner">
                            <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-125 transition duration-1000">
                        </div>
                        <div class="flex-1 space-y-4 lg:space-y-6">
                            <div class="space-y-1">
                                <h3 class="text-xl lg:text-2xl font-black text-gray-900 leading-tight tracking-tight line-clamp-1">{{ $product->name }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">🏪 {{ $product->store->name ?? 'Arradea' }}</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-2xl lg:text-3xl font-black text-gray-900 italic tracking-tighter">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <a href="/products/{{ $product->id }}" class="w-full h-12 lg:h-16 bg-primary-600 text-white rounded-xl lg:rounded-[1.5rem] font-black text-sm lg:text-lg hover:bg-primary-700 shadow-xl lg:shadow-2xl shadow-primary-200 transition-all lg:opacity-0 lg:group-hover:opacity-100 lg:translate-y-10 lg:group-hover:translate-y-0 flex items-center justify-center">
                                + Beli Sekarang
                            </a>
                        </div>
                    </div>
                @empty
                    @for($m=1; $m<=4; $m++)
                        <div class="animate-pulse bg-white rounded-3xl lg:rounded-[4rem] p-6 lg:p-10 h-[500px]">
                            <div class="bg-gray-100 rounded-2xl lg:rounded-[3rem] h-64 mb-8"></div>
                            <div class="h-8 bg-gray-100 rounded-full w-3/4 mb-4"></div>
                            <div class="h-4 bg-gray-100 rounded-full w-1/2"></div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>
</div>

<style>
    @keyframes fade-in { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 1s ease-out; }
</style>
@endsection
