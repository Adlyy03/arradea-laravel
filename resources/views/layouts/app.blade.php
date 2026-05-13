<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Arradea">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Arradea">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/logo-arradea.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/logo-arradea.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/logo-arradea.png">
    <title>@yield('title', 'Arradea Marketplace')</title>
    
    {{-- Flash Messages for Toast System --}}
    @if(session('success'))
        <meta name="flash-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="flash-error" content="{{ session('error') }}">
    @endif
    @if(session('warning'))
        <meta name="flash-warning" content="{{ session('warning') }}">
    @endif
    @if(session('info'))
        <meta name="flash-info" content="{{ session('info') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Mobile Menu Fixes */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Ensure body is visible */
        body {
            min-height: 100vh;
            background: #f7faf7 !important;
        }
        
        /* Ensure main content is visible */
        main {
            position: relative;
            z-index: 1;
        }
        
        /* Ensure body scroll lock works */
        body.overflow-hidden {
            overflow: hidden !important;
        }
        
        /* Mobile menu overlay */
        .mobile-menu-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        
        /* Ensure hamburger button is always clickable */
        nav button[aria-label="Toggle menu"] {
            position: relative;
            z-index: 60;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Mobile menu positioning */
        nav > div:last-child {
            position: relative;
            z-index: 50;
        }
        
        /* Prevent double-tap zoom on buttons */
        button {
            touch-action: manipulation;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-[#f7faf7] text-gray-900 font-sans antialiased" 
      x-data="{ mobileOpen: false }" 
      :class="{ 'overflow-hidden': mobileOpen }"
      @keydown.escape.window="mobileOpen = false">

    {{-- Mobile Menu Overlay --}}
    <div x-show="mobileOpen" 
         x-cloak
         @click="mobileOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 md:hidden">
    </div>

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 glass border-b border-green-100/60 shadow-sm shadow-green-100/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
<<<<<<< HEAD
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('images/arradea.jpeg') }}" alt="Arradea" class="w-8 h-8 rounded-xl object-cover shadow-md shadow-green-300/40 group-hover:scale-105 transition">
=======
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <img src="/icons/logo-arradea.png" alt="Arradea" class="w-8 h-8 rounded-xl object-cover shadow-md group-hover:scale-105 transition">
>>>>>>> 1688c02551a4c3a5c36573e09b0fed8b8d385f24
                    <span class="text-xl font-black text-gray-900 tracking-tight">Arradea<span class="text-sage">.</span></span>
                </a>

                {{-- Search --}}
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <svg class="absolute left-3.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" placeholder="Cari produk, toko..." class="w-full h-9 bg-gray-100/80 border border-gray-200/60 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-sage/40 focus:border-sage/60 transition-all">
                    </div>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('categories.index') }}" class="nav-link text-sm font-medium text-gray-600 hover:text-gray-900">Kategori</a>

                    @auth
                        @if(Auth::user()->role !== 'admin')
                            <a href="{{ route('buyer.cart') }}" class="relative text-gray-500 hover:text-sage transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12m-10-5a2 2 0 104 0m6 0a2 2 0 104 0"/></svg>
                                @if(Auth::user()->carts->count() > 0)
                                    <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-sage text-white text-[9px] font-black rounded-full flex items-center justify-center">{{ Auth::user()->carts->count() }}</span>
                                @endif
                            </a>
                        @endif

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open=!open" class="flex items-center gap-2 hover:opacity-80 transition">
                                <div class="w-8 h-8 rounded-xl bg-sage/15 border border-sage/30 flex items-center justify-center text-sage font-black text-sm">
                                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="open && 'rotate-180'" style="transition:.2s" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open=false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50">
                                <div class="px-4 py-2.5 border-b border-gray-50">
                                    <p class="text-xs font-black text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">{{ Auth::user()->is_seller ? 'Seller + Buyer' : 'Buyer' }}</p>
                                </div>

                                {{-- Mode Switcher (only for sellers) --}}
                                @if(Auth::user()->canSwitchToSellerMode())
                                    <div class="px-3 py-3 border-b border-gray-50">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-2 px-1">Mode Aktif</p>
                                        <x-bottom-sheet-switcher :user="Auth::user()" />
                                    </div>
                                @endif

                                @if(Auth::user()->is_seller)
                                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Dashboard Seller
                                    </a>
                                @elseif(Auth::user()->role === 'admin')
                                    <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Admin Panel
                                    </a>
                                @else
                                    <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil Saya
                                </a>
                                <div class="border-t border-gray-50 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary text-white text-sm font-semibold px-4 py-2 rounded-xl">Daftar Gratis</a>
                    @endauth

                    {{-- PWA install button (explicit) --}}
                    <button id="pwa-install-btn" type="button" class="hidden md:inline-flex items-center gap-2 px-3 py-2 rounded-full bg-black text-white text-sm font-semibold ml-2 transition" aria-hidden="true">Tambahkan ke Beranda</button>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" 
                        type="button"
                        class="md:hidden p-2 rounded-xl hover:bg-gray-100 transition text-gray-600 relative z-50"
                        :aria-expanded="mobileOpen"
                        aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" 
                         class="w-5 h-5" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" 
                         x-cloak 
                         class="w-5 h-5" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" 
             x-cloak
             @click.away="mobileOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-3"
             class="md:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-1 relative z-50">
            <div class="relative mb-3">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari produk..." class="w-full h-10 bg-gray-100 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-sage/40">
            </div>
            <a href="{{ route('categories.index') }}" 
               @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kategori
            </a>
            @auth
                <div class="flex items-center gap-3 px-3 py-3 bg-gray-50 rounded-xl mb-2">
                    <div class="w-9 h-9 rounded-xl bg-sage/15 flex items-center justify-center text-sage font-black">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase">{{ Auth::user()->is_seller ? 'Seller' : 'Buyer' }}</p>
                    </div>
                </div>
                @if(Auth::user()->role !== 'admin')
                    <a href="{{ route('buyer.cart') }}" 
                       @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
                        Keranjang
                        @if(Auth::user()->carts->count() > 0)
                            <span class="ml-auto bg-sage text-white text-[10px] font-black px-2 py-0.5 rounded-lg">{{ Auth::user()->carts->count() }}</span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('profile') }}" 
                   @click="mobileOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Profil Saya
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" 
                   @click="mobileOpen = false"
                   class="block px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Masuk
                </a>
                <a href="{{ route('register') }}" 
                   @click="mobileOpen = false"
                   class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-white btn-primary text-center">
                    Daftar Gratis
                </a>
            @endauth
        </div>
    </nav>

    <main class="min-h-[70vh] px-4 sm:px-5 lg:px-8 py-4 lg:py-6 overflow-x-hidden">
        <div class="mx-auto w-full max-w-7xl">
            @yield('content')
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
<<<<<<< HEAD
                        <img src="{{ asset('images/arradea.jpeg') }}" alt="Arradea" class="w-7 h-7 rounded-lg object-cover shadow-sm">
=======
                        <img src="/icons/logo-arradea.png" alt="Arradea" class="w-7 h-7 rounded-lg object-cover">
>>>>>>> 1688c02551a4c3a5c36573e09b0fed8b8d385f24
                        <span class="text-lg font-black text-gray-900">Arradea<span class="text-sage">.</span></span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Marketplace warga Arradea. Belanja dari tetangga, untuk tetangga.</p>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Belanja</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('categories.index') }}" class="hover:text-sage transition">Kategori</a></li>
                        <li><a href="{{ url('/') }}" class="hover:text-sage transition">Produk Terbaru</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Akun</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        @auth
                            <li><a href="{{ route('profile') }}" class="hover:text-sage transition">Profil</a></li>
                            @if(!Auth::user()->is_seller && Auth::user()->role !== 'admin')
                                <li><a href="{{ route('seller.apply') }}" class="hover:text-sage transition">Jadi Seller</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-sage transition">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-sage transition">Daftar</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Seller</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('seller.apply') }}" class="hover:text-sage transition">Daftar Seller</a></li>
                        <li><a href="{{ route('seller.dashboard') }}" class="hover:text-sage transition">Seller Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-gray-400">© {{ date('Y') }} Arradea Marketplace. Semua hak dilindungi.</p>
                <p class="text-xs text-gray-400">Dibuat dengan <span class="text-sage">♥</span> untuk warga Arradea</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // SweetAlert2 wrapper
    (function(){
        const base={background:'#fff',color:'#111827',customClass:{popup:'rounded-2xl shadow-2xl',title:'text-lg font-black',htmlContainer:'text-sm text-gray-500',confirmButton:'rounded-xl px-5 py-2.5 font-bold text-sm',cancelButton:'rounded-xl px-5 py-2.5 font-bold text-sm'},buttonsStyling:false};
        window.arradeaPopup={
            _fire(c){return typeof Swal!=='undefined'?Swal.fire({...base,...c}):null},
            success(msg,title){return this._fire({icon:'success',iconColor:'#72bf77',title:title||'✅ Berhasil',text:msg,confirmButtonColor:'#72bf77'})},
            error(msg,title){return this._fire({icon:'error',iconColor:'#dc2626',title:title||'❌ Gagal',text:msg,confirmButtonColor:'#dc2626'})},
            confirm(msg,opts={}){
                if(typeof Swal==='undefined')return Promise.resolve(false);
                return Swal.fire({...base,icon:'warning',iconColor:'#f59e0b',title:opts.title||'Konfirmasi',text:msg||'Lanjutkan?',showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, lanjut',cancelButtonText:'Batal',confirmButtonColor:opts.confirmColor||'#72bf77',reverseButtons:true}).then(r=>r.isConfirmed);
            },
            danger(msg,opts={}){
                if(typeof Swal==='undefined')return Promise.resolve(false);
                return Swal.fire({...base,icon:'warning',iconColor:'#dc2626',title:opts.title||'⚠️ Hapus?',text:msg,showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626',reverseButtons:true}).then(r=>r.isConfirmed);
            }
        };
        window.confirmSubmit=function(e,msg){
            e&&e.preventDefault();
            const form=e&&e.target;
            if(!form)return false;
            window.arradeaPopup.danger(msg).then(ok=>{if(ok)form.submit()});
            return false;
        };
    })();
    </script>
    @stack('scripts')

    <script defer src="/js/pwa.js"></script>
</body>
</html>
