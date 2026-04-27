<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Arradea Marketplace')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Tailwind CSS via CDN for instant preview -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: '#ff4d00',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .dark-glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div id="app" x-data="{ openMenu: false, user: {{ Auth::check() ? Auth::user() : 'null' }} }">
        
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 glass border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-3xl font-extrabold tracking-tighter text-primary-600">
                            Arradea<span class="text-accent">.</span>
                        </a>
                    </div>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-lg mx-10">
                        <div class="relative w-full">
                            <input type="text" class="w-full bg-gray-100 border-none rounded-2xl py-3 px-5 focus:ring-2 focus:ring-primary-500 text-sm transition-all" placeholder="Cari produk impianmu...">
                            <div class="absolute right-4 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                        <a href="#" class="text-gray-600 hover:text-primary-600 transition">Kategori</a>
                        <a href="#" class="text-gray-600 hover:text-primary-600 transition">Promo</a>
                        
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="text-gray-700">{{ explode(' ', Auth::user()->name)[0] }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2">
                                    <div class="px-4 py-2 border-b border-gray-50 mb-2">
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Mode: {{ Auth::user()->is_seller ? 'SELLER + BUYER' : 'BUYER' }}</p>
                                    </div>
                                    
                                    @if(Auth::user()->is_seller)
                                        <a href="/seller/dashboard" class="block px-4 py-2 hover:bg-primary-50 text-gray-700">Toko Saya</a>
                                    @elseif(Auth::user()->role === 'admin')
                                        <a href="/admin/dashboard" class="block px-4 py-2 hover:bg-primary-50 text-gray-700">Admin Panel</a>
                                    @endif

                                    <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-primary-50 text-gray-700">Profil Saya</a>
                                    <a href="/orders" class="block px-4 py-2 hover:bg-primary-50 text-gray-700">Pesanan Saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-bold">Keluar</button>
                                    </form>
                                </div>
                            </div>

                            @if(Auth::user()->role !== 'admin')
                                <a href="{{ route('buyer.cart') }}" class="relative text-gray-600 hover:text-primary-600 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13h10m0 0v8a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @if(Auth::user()->carts->count() > 0)
                                        <span class="absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ Auth::user()->carts->count() }}</span>
                                    @endif
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 transition">Masuk</a>
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all font-bold">Daftar</a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center space-x-4">
                        @auth
                            @if(Auth::user()->role !== 'admin')
                                <a href="{{ route('buyer.cart') }}" class="relative text-gray-600">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13h10m0 0v8a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @if(Auth::user()->carts->count() > 0)
                                        <span class="absolute -top-1 -right-1 bg-accent text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">{{ Auth::user()->carts->count() }}</span>
                                    @endif
                                </a>
                            @endif
                        @endauth
                        <button @click="openMenu = !openMenu" class="text-gray-500 focus:outline-none focus:text-primary-600 p-1">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!openMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="openMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="openMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="md:hidden bg-white border-t border-gray-100 px-6 py-8 space-y-6 shadow-2xl">
                <div class="relative w-full">
                    <input type="text" class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary-500 text-sm transition-all" placeholder="Cari produk impianmu...">
                    <div class="absolute right-5 top-4 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
                
                <div class="space-y-2">
                    @auth
                        <div class="flex items-center space-x-4 p-4 bg-primary-50 rounded-2xl mb-4">
                            <div class="w-12 h-12 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] uppercase font-black text-primary-600 tracking-widest">{{ Auth::user()->is_seller ? 'seller + buyer' : 'buyer' }}</p>
                            </div>
                        </div>

                        @if(Auth::user()->is_seller)
                            <a href="/seller/dashboard" class="flex items-center space-x-4 p-4 hover:bg-gray-50 rounded-2xl transition font-bold text-gray-700">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Toko Saya (Dashboard)</span>
                            </a>
                        @elseif(Auth::user()->role === 'admin')
                            <a href="/admin/dashboard" class="flex items-center space-x-4 p-4 hover:bg-gray-50 rounded-2xl transition font-bold text-gray-700">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Admin Panel</span>
                            </a>
                        @endif

                        <a href="{{ route('profile') }}" class="flex items-center space-x-4 p-4 hover:bg-gray-50 rounded-2xl transition font-bold text-gray-700">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A12.072 12.072 0 0112 15c2.533 0 4.897.856 6.879 2.304M15 11a3 3 0 11-6 0 3 3 0 016 0zM7 20h10"/></svg>
                            <span>Profil Saya</span>
                        </a>
                        <a href="/orders" class="flex items-center space-x-4 p-4 hover:bg-gray-50 rounded-2xl transition font-bold text-gray-700">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Pesanan Saya</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="w-full pt-4">
                            @csrf
                            <button type="submit" class="w-full px-6 py-4 bg-red-50 text-red-600 rounded-2xl font-black flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Keluar Sekarang</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full px-6 py-4 bg-gray-50 text-center rounded-2xl font-black text-gray-700 border border-gray-100">Masuk Akun</a>
                        <a href="{{ route('register') }}" class="block w-full px-6 py-4 bg-primary-600 text-white text-center rounded-2xl font-black shadow-xl shadow-primary-200">Daftar Sekarang</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="min-h-[70vh]">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-16 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="col-span-1 md:col-span-1">
                        <a href="#" class="text-3xl font-extrabold text-primary-600">Arradea<span class="text-accent">.</span></a>
                        <p class="mt-4 text-gray-500 leading-relaxed">Arradea adalah destinasi marketplace premium di Indonesia yang menghadirkan produk kurasi terbaik untuk gaya hidup modern Anda.</p>
                        <div class="mt-6 flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary-600 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary-600 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Belanja</h4>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Semua Produk</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Kategori</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Promo Menarik</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Flash Sale</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Bantuan</h4>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Cara Berbelanja</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Pengiriman</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Kontak Kami</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Seller</h4>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('seller.apply') }}" class="text-gray-600 hover:text-primary-600">Daftar Jadi Seller</a></li>
                            <li><a href="/seller/dashboard" class="text-gray-600 hover:text-primary-600">Seller Center</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">Tips Berjualan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">© 2026 Arradea Marketplace. Built with ❤️ for premium experience.</p>
                    <div class="mt-4 md:mt-0 flex space-x-6 text-sm text-gray-400">
                        <a href="#" class="hover:text-primary-600">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-primary-600">Syarat Penggunaan</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const hasSwal = () => typeof window.Swal !== 'undefined';

            const baseOptions = {
                background: '#ffffff',
                color: '#111827',
                confirmButtonColor: '#0284c7',
                customClass: {
                    popup: 'rounded-3xl px-6 py-6',
                    title: 'text-xl font-black',
                    htmlContainer: 'text-sm font-medium text-gray-600',
                    confirmButton: 'rounded-xl px-5 py-3 font-black',
                    cancelButton: 'rounded-xl px-5 py-3 font-black',
                },
                buttonsStyling: false,
            };

            window.arradeaPopup = {
                _firePopup(config) {
                    if (!hasSwal()) return;
                    return window.Swal.fire({ ...baseOptions, ...config });
                },

                success(message, title) {
                    return this._firePopup({
                        icon: 'success',
                        iconColor: '#16a34a',
                        title: title || '✅ Berhasil',
                        text: message || 'Operasi berhasil dilakukan.',
                        confirmButtonColor: '#16a34a',
                        backdrop: 'rgba(22, 163, 74, 0.1)',
                    });
                },

                error(message, title) {
                    return this._firePopup({
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: title || '❌ Gagal',
                        text: message || 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonColor: '#dc2626',
                        backdrop: 'rgba(220, 38, 38, 0.1)',
                    });
                },

                danger(message, options) {
                    const resolvedOptions = options || {};
                    return this._firePopup({
                        icon: 'warning',
                        iconColor: '#dc2626',
                        title: resolvedOptions.title || '⚠️ Perhatian',
                        text: message || 'Aksi ini tidak bisa dibatalkan.',
                        showCancelButton: true,
                        confirmButtonText: resolvedOptions.confirmText || 'Ya, hapus',
                        cancelButtonText: resolvedOptions.cancelText || 'Batal',
                        confirmButtonColor: '#dc2626',
                        backdrop: 'rgba(220, 38, 38, 0.1)',
                        reverseButtons: true,
                    }).then((result) => Boolean(result.isConfirmed));
                },

                info(message, title) {
                    return this._firePopup({
                        icon: 'info',
                        iconColor: '#0284c7',
                        title: title || 'ℹ️ Informasi',
                        text: message || 'Perhatian informasi penting.',
                        confirmButtonColor: '#0284c7',
                        backdrop: 'rgba(2, 132, 199, 0.1)',
                    });
                },

                confirm(message, options) {
                    const safeMessage = message || 'Apakah Anda yakin?';
                    const resolvedOptions = options || {};

                    if (!hasSwal()) {
                        return Promise.resolve(false);
                    }

                    return window.Swal.fire({
                        ...baseOptions,
                        icon: resolvedOptions.icon || 'warning',
                        iconColor: resolvedOptions.iconColor || '#ea580c',
                        title: resolvedOptions.title || '🤔 Konfirmasi',
                        text: safeMessage,
                        showCancelButton: true,
                        confirmButtonText: resolvedOptions.confirmText || 'Ya, lanjut',
                        cancelButtonText: resolvedOptions.cancelText || 'Batal',
                        confirmButtonColor: resolvedOptions.confirmColor || '#0284c7',
                        backdrop: 'rgba(234, 88, 12, 0.1)',
                        reverseButtons: true,
                    }).then((result) => Boolean(result.isConfirmed));
                },
            };

            window.confirmSubmit = function (event, message) {
                if (event) {
                    event.preventDefault();
                }

                const form = event && event.target ? event.target : null;
                if (!form) {
                    return false;
                }

                window.arradeaPopup.confirm(message).then((isConfirmed) => {
                    if (isConfirmed) {
                        form.submit();
                    }
                });

                return false;
            };
        })();
    </script>
</body>
</html>
