<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Arradea Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) + Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' },
                        accent: '#ff4d00',
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .sidebar-active { @apply bg-primary-600 text-white shadow-xl shadow-primary-200; }
    </style>
</head>
<body class="bg-gray-50 font-['Plus_Jakarta_Sans'] text-gray-900 overflow-x-hidden antialiased">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: window.innerWidth > 1024, openChatsModal: false }">
        
        <!-- SIDEBAR BACKDROP (Mobile Only) -->
        <div 
            x-show="sidebarOpen && window.innerWidth < 1024" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-primary-900/40 backdrop-blur-sm z-50 lg:hidden"
            x-cloak
        ></div>

        <!-- SIDEBAR -->
        <aside 
            :class="{
                'w-80 translate-x-0': sidebarOpen,
                'w-24 translate-x-0': !sidebarOpen && window.innerWidth >= 1024,
                '-translate-x-full': !sidebarOpen && window.innerWidth < 1024
            }"
            class="bg-white border-r border-gray-100 flex-shrink-0 transition-all duration-300 flex flex-col fixed h-screen top-0 z-[60] overflow-y-auto overflow-x-hidden transform lg:sticky lg:top-0"
        >
            <div class="p-8 flex items-center justify-between">
                <a href="/" class="text-3xl font-black text-primary-600 tracking-tighter" x-show="sidebarOpen || window.innerWidth < 1024">
                    Arradea<span class="text-accent">.</span>
                </a>
                <span class="text-3xl font-black text-primary-600 mx-auto" x-show="!sidebarOpen && window.innerWidth >= 1024">A.</span>
                
                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 px-6 space-y-3 mt-4">
                <!-- User Info -->
                <div class="p-4 bg-gray-50 rounded-[2rem] mb-8" x-show="sidebarOpen || window.innerWidth < 1024">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-black">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-black truncate text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->role === 'buyer')
                    <x-sidebar.buyer />
                @elseif(Auth::user()->role === 'seller')
                    <x-sidebar.seller />
                @elseif(Auth::user()->role === 'admin')
                    <x-sidebar.admin />
                @endif
            </nav>

            <!-- Logout -->
            <div class="p-8 mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-red-50 text-red-600 rounded-3xl font-black text-sm hover:bg-red-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarOpen || window.innerWidth < 1024">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN LAYOUT -->
        <main class="flex-1 min-h-screen relative w-full lg:transition-all lg:duration-300 pb-32 lg:pb-8">
            <!-- TOP BAR -->
            <header class="h-24 sticky top-0 z-40 bg-white/80 backdrop-blur-md px-6 lg:px-12 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center gap-4 lg:gap-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-500 hover:bg-gray-100 transition shadow-sm border border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                    </button>
                    <h2 class="text-lg lg:text-xl font-black text-gray-900 tracking-tight line-clamp-1">@yield('page_title', 'Dashboard Monitoring')</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="relative w-72 hidden xl:block">
                        <input type="text" placeholder="Cari di dashboard..." class="w-full h-12 bg-gray-50 border-none rounded-2xl px-6 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                        <div class="absolute right-4 top-3 text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="w-11 lg:w-12 h-11 lg:h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 relative">
                        <div class="absolute top-3 right-3 w-2 h-2 bg-accent rounded-full"></div>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/></svg>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <div class="p-6 lg:p-12">
                @if(session('success'))
                    <div class="mb-10 p-6 bg-green-50 border border-green-100 rounded-[2rem] flex items-center gap-4 text-green-700 shadow-sm animate-pulse">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">✓</div>
                        <p class="font-bold">{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    <!-- BOTTOM NAVIGATION (Mobile Only) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-t border-gray-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] pb-safe rounded-t-3xl">
        <div class="flex items-center justify-around px-2 py-3 mx-auto max-w-sm">
            @if(Auth::user()->role === 'buyer')
                <!-- Buyer Bottom Tabs -->
                <a href="{{ route('buyer.dashboard') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 {{ Request::is('buyer/dashboard') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('buyer/dashboard') ? 'text-primary-600' : 'text-gray-400' }}">Beranda</span>
                </a>

                <a href="{{ route('buyer.products') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 {{ Request::is('products*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21H3V5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H17a2 2 0 012 2v14zM9 9h6m-6 4h6m-5 5h4"/></svg>
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('products*') ? 'text-primary-600' : 'text-gray-400' }}">Belanja</span>
                </a>

                <a href="{{ route('buyer.cart') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 relative {{ Request::is('cart*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 6.146A1 1 0 006.7 21h10.6a1 1 0 00.894-.854L19 8m-6 7v6m-4-6v6"/></svg>
                        @php $cartCount = Auth::user()->carts->count(); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-[9px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-black border-2 border-white">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('cart*') ? 'text-primary-600' : 'text-gray-400' }}">Keranjang</span>
                </a>

                <a href="{{ route('buyer.orders') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 relative {{ Request::is('orders*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @php $pendingOrderCount = Auth::user()->orders()->whereIn('status', ['pending', 'accepted'])->count(); @endphp
                        @if($pendingOrderCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-[9px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-black border-2 border-white">{{ $pendingOrderCount > 9 ? '9+' : $pendingOrderCount }}</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('orders*') ? 'text-primary-600' : 'text-gray-400' }}">Pesanan</span>
                </a>

                <button @click="openChatsModal = true" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 relative text-gray-400 group-hover:text-primary-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @php $unreadMessagesCount = \App\Models\Message::whereHas('chat', function ($q) { $q->where('buyer_id', Auth::id()); })->where('sender_id', '!=', Auth::id())->where('is_read', false)->count(); @endphp
                        @if($unreadMessagesCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-[9px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-black border-2 border-white">{{ $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount }}</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold mt-1 text-gray-400">Chat</span>
                </button>
            @endif

            @if(Auth::user()->role === 'seller')
                <!-- Seller Bottom Tabs -->
                <a href="{{ route('seller.dashboard') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 {{ Request::is('seller/dashboard') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('seller/dashboard') ? 'text-primary-600' : 'text-gray-400' }}">Beranda</span>
                </a>

                <a href="/seller/products" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 {{ Request::is('seller/products*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('seller/products*') ? 'text-primary-600' : 'text-gray-400' }}">Produk</span>
                </a>

                <a href="/seller/orders" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 relative {{ Request::is('seller/orders*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @php $pendingOrderCount = Auth::user()->store ? Auth::user()->store->orders()->where('status', 'pending')->count() : 0; @endphp
                        @if($pendingOrderCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-[9px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-black border-2 border-white">{{ $pendingOrderCount > 9 ? '9+' : $pendingOrderCount }}</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('seller/orders*') ? 'text-primary-600' : 'text-gray-400' }}">Order</span>
                </a>

                <a href="{{ route('seller.analytics') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 {{ Request::is('seller/analytics*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('seller/analytics*') ? 'text-primary-600' : 'text-gray-400' }}">Data</span>
                </a>

                <a href="{{ route('seller.messages') }}" class="group flex-1 flex flex-col items-center justify-center relative">
                    <div class="p-2 rounded-2xl transition-all duration-300 relative {{ Request::is('seller/messages*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @php $unreadMessagesCount = \App\Models\Message::whereHas('chat', function ($q) { $q->where('seller_id', Auth::id()); })->where('sender_id', '!=', Auth::id())->where('is_read', false)->count(); @endphp
                        @if($unreadMessagesCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-[9px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-black border-2 border-white">{{ $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount }}</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold mt-1 transition-colors {{ Request::is('seller/messages*') ? 'text-primary-600' : 'text-gray-400' }}">Pesan</span>
                </a>
            @endif
        </div>
    </nav>

    <!-- BUYER CHATS MODAL -->
    @if(Auth::check() && Auth::user()->role === 'buyer')
    <div x-show="openChatsModal" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200" @click="openChatsModal = false" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm" x-cloak>
        <div @click.stop class="absolute right-0 top-0 w-full md:w-96 h-full bg-white shadow-xl flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-black">💬 Chat Seller</h3>
                <button @click="openChatsModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Chats List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @php
                    $chats = \App\Models\Chat::where('buyer_id', Auth::id())
                        ->with(['order.product', 'order.store.user', 'messages'])
                        ->latest()
                        ->get();
                @endphp

                @forelse($chats as $chat)
                    @php
                        $unreadCount = $chat->messages()
                            ->where('sender_id', '!=', Auth::id())
                            ->where('is_read', false)
                            ->count();
                        $lastMessage = $chat->messages()->latest()->first();
                    @endphp
                    <a href="{{ route('chat.show', $chat->order) }}" @click="openChatsModal = false" class="block p-4 rounded-xl hover:bg-gray-50 border border-gray-100 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-black flex-shrink-0">
                                {{ strtoupper(substr($chat->order->store->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-sm truncate">{{ $chat->order->store->user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $lastMessage->message ?? 'Mulai berbincang' }}</p>
                            </div>
                            @if($unreadCount > 0)
                                <span class="bg-accent text-white text-[10px] font-black px-2 py-1 rounded-lg flex-shrink-0">{{ $unreadCount }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-sm">Belum ada percakapan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</body>
</html>
