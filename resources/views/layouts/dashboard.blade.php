<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Arradea Dashboard')</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f0faf1',100:'#d8f3da',200:'#b3e6b8',300:'#7fd189',400:'#4db85a',500:'#72bf77',600:'#3fa348',700:'#2d7a34',800:'#255f2a',900:'#1a4220' },
                        sage: '#72bf77',
                        dark: '#0f1911',
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans','sans-serif'], dm: ['DM Sans','sans-serif'] },
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/mobile-optimizations.css') }}">
    <style>
        [x-cloak]{display:none!important}
        *{-webkit-font-smoothing:antialiased}
        ::-webkit-scrollbar{width:4px;height:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#2d4a30;border-radius:10px}

        /* Sidebar visibility control */
        .sidebar-hidden{transform:translateX(-100%) !important}
        .sidebar-visible{transform:translateX(0) !important}

        /* ── Sidebar Core ─────────────────────────────── */
        .sb-item{
            display:flex;align-items:center;gap:12px;
            padding:10px 12px;
            border-radius:12px;
            transition:all .25s cubic-bezier(.4,0,.2,1);
            font-weight:600;font-size:.875rem;
            color:rgba(255,255,255,.9);
            cursor:pointer;text-decoration:none;
            position:relative;
            border:1px solid transparent;
        }
        .sb-item:hover{
            background:rgba(255,255,255,.15);
            color:white;
            border-color:rgba(255,255,255,.25);
            transform:translateX(2px);
        }
        .sb-item.sb-active{
            background:rgba(255,255,255,.2);
            color:white;
            border-color:rgba(255,255,255,.3);
            box-shadow:0 4px 12px rgba(0,0,0,.2);
        }
        .sb-item.sb-active .sb-icon svg{color:white;opacity:1}
        .sb-item:hover .sb-icon svg{opacity:1;color:white}

        /* Icon container */
        .sb-icon{
            width:36px;height:36px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;
            border-radius:10px;
            background:rgba(255,255,255,.12);
            transition:all .25s;
        }
        .sb-item.sb-active .sb-icon{
            background:rgba(255,255,255,.25);
        }
        .sb-item:hover .sb-icon{
            background:rgba(255,255,255,.2);
        }
        .sb-icon svg{width:18px;height:18px;opacity:.85;transition:all .25s;flex-shrink:0;color:white}

        /* Label */
        .sb-label{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

        /* Section Labels */
        .sb-section-label{
            padding:16px 12px 6px;
            font-size:.65rem;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:rgba(255,255,255,.5);
            display:flex;align-items:center;gap:8px;
        }
        .sb-section-label::after{
            content:'';flex:1;height:1px;
            background:rgba(255,255,255,.15);
        }

        /* Badges */
        .sb-badge{
            font-size:.7rem;font-weight:800;
            padding:3px 8px;
            border-radius:20px;
            flex-shrink:0;
            line-height:1.3;
        }
        .sb-badge-green{background:rgba(255,255,255,.25);color:white}
        .sb-badge-amber{background:#f59e0b;color:white}
        .sb-badge-red{background:#dc2626;color:white}

        /* Icon dot (collapsed state) */
        .sb-dot{
            position:absolute;top:8px;right:8px;
            width:8px;height:8px;border-radius:50%;
            border:2px solid #1e5128;
        }
        .sb-dot-amber{background:#f59e0b}
        .sb-dot-red{background:#dc2626}

        /* Icon overlay dot */
        .sb-icon-dot{
            position:absolute;top:-3px;right:-3px;
            width:8px;height:8px;border-radius:50%;
            background:white;
            border:2px solid #1e5128;
        }
        .sb-icon-dot-red{background:#dc2626}

        /* Status chips */
        .sb-status-chip{
            margin:8px 6px 4px;
            padding:10px 12px;
            border-radius:12px;
            display:flex;align-items:flex-start;gap:10px;
        }
        .sb-chip-amber{background:rgba(245,158,11,.25);border:1px solid rgba(245,158,11,.4)}
        .sb-chip-green{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25)}
        .sb-chip-dot{
            width:8px;height:8px;border-radius:50%;
            flex-shrink:0;margin-top:4px;
        }
        .sb-chip-amber .sb-chip-dot{background:#f59e0b}
        .sb-chip-green .sb-chip-dot{background:white}
        .sb-chip-title{
            font-size:.7rem;font-weight:800;
            text-transform:uppercase;letter-spacing:.08em;
            margin:0;color:white;
        }
        .sb-chip-desc{font-size:.7rem;margin:3px 0 0;color:rgba(255,255,255,.75)}

        /* Topbar */
        .topbar-glass{background:rgba(247,250,247,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}

        /* Stat card */
        .stat-card{background:rgba(255,255,255,.8);border:1px solid rgba(114,191,119,.15);border-radius:16px;padding:20px;transition:all .25s;cursor:default}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(114,191,119,.15);border-color:rgba(114,191,119,.35)}

        @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .fade-up{animation:fadeUp .35s ease both}
        .badge-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0}

        /* Mobile optimizations */
        @media(max-width:1023px){
            .sb-section-label{padding-top:12px;padding-bottom:4px;font-size:.6rem}
            .sb-item{padding:8px 10px;font-size:.8rem}
            .sb-icon{width:32px;height:32px}
            .sb-icon svg{width:16px;height:16px}
            .sb-badge{font-size:.65rem;padding:2px 6px}
        }
        
        /* Bottom navigation styles */
        .bottom-nav{
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            border-top:1px solid rgba(114,191,119,0.1);
            box-shadow:0 -4px 20px rgba(0,0,0,0.08);
        }
        .bottom-nav-item{
            display:flex;flex-direction:column;align-items:center;gap:2px;
            padding:6px 4px;border-radius:8px;transition:all .2s;
            text-decoration:none;min-width:0;flex:1;
        }
        .bottom-nav-item:hover,.bottom-nav-item.active{
            background:rgba(114,191,119,0.1);
            color:#72bf77;
        }
        .bottom-nav-icon{width:20px;height:20px;flex-shrink:0}
        .bottom-nav-label{font-size:9px;font-weight:700;line-height:1.2;text-align:center}
        .bottom-nav-badge{
            position:absolute;top:-2px;right:-2px;
            background:#72bf77;color:white;
            font-size:7px;font-weight:800;
            width:14px;height:14px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid white;
        }
        
        /* Floating button animation */
        .floating-chat{
            animation:float 3s ease-in-out infinite;
        }
        @keyframes float{
            0%,100%{transform:translateY(0px)}
            50%{transform:translateY(-6px)}
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f2f5f2] font-sans text-gray-900 overflow-x-hidden" 
      x-data="{ 
          sideOpen: window.innerWidth >= 1024, 
          chatModal: false,
          isMobile: window.innerWidth < 1024
      }"
      @resize.window="isMobile = window.innerWidth < 1024; if (!isMobile) sideOpen = true; else sideOpen = false;">

{{-- SIDEBAR - HIJAU GELAP GRADASI --}}
<aside 
    x-cloak
    class="fixed top-0 left-0 h-screen flex flex-col overflow-hidden transition-all duration-300 ease-out shadow-2xl"
    :style="isMobile ? (sideOpen ? 'width:240px; transform:translateX(0); z-index:50; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)' : 'width:240px; transform:translateX(-100%); z-index:50; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)') : (sideOpen ? 'width:200px; transform:translateX(0); z-index:30; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)' : 'width:50px; transform:translateX(0); z-index:30; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)')"
    style="border-right:2px solid #72bf77">

    {{-- Logo area --}}
    <div class="flex items-center justify-between px-4 h-[60px] flex-shrink-0 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-base shadow-lg" style="background:white;color:#1e5128">
                A
            </div>
            <div x-show="sideOpen" x-cloak class="overflow-hidden">
                <span class="text-white font-black text-base tracking-tight block">Arradea</span>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-white/70">Marketplace</span>
            </div>
        </div>
        {{-- Close button for mobile --}}
        <button @click="sideOpen=false" 
                x-show="sideOpen && isMobile" 
                class="lg:hidden w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- User info card --}}
    <div class="px-3 pt-4 pb-2 flex-shrink-0">
        <div x-show="sideOpen" x-cloak
            class="flex items-center gap-3 p-3 rounded-xl bg-white/10 border border-white/20 backdrop-blur-sm">
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-sm bg-white shadow-md" style="color:#1e5128">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
            <div class="overflow-hidden flex-1 min-w-0">
                <p class="text-white text-sm font-bold truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] uppercase tracking-wide font-semibold truncate mt-1 text-white/60">
                    @if(Auth::user()->role==='admin')
                        Admin
                    @elseif(Auth::user()->is_seller)
                        {{ Auth::user()->store?->name ?? 'Seller' }}
                    @else
                        Buyer
                    @endif
                </p>
            </div>
        </div>
        {{-- Collapsed: just avatar --}}
        <div x-show="!sideOpen" class="flex justify-center">
            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-sm bg-white shadow-md" style="color:#1e5128">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-2 space-y-1">
        @if(Auth::user()->role === 'admin')
            @include('components.sidebar.admin')
        @else
            @include('components.sidebar.buyer')
            @if(Auth::user()->is_seller)
                <div class="mt-2 pt-1" style="border-top:1px solid rgba(114,191,119,.08)">
                    @include('components.sidebar.seller')
                </div>
            @endif
        @endif
    </nav>

    {{-- Logout --}}
    <div class="p-3 flex-shrink-0 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
            <button type="button" 
                    onclick="confirmLogout(event)" 
                    class="sb-item w-full text-left text-white/80 hover:bg-red-500/90 hover:text-white hover:border-red-400">
                <span class="sb-icon bg-white/10">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </span>
                <span x-show="sideOpen" x-cloak class="sb-label">Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<div class="min-h-screen flex flex-col transition-all duration-300 ease-out" 
     :class="isMobile ? 'ml-0 pb-20' : (sideOpen ? 'ml-[200px]' : 'ml-[50px]')">

    {{-- TOPBAR --}}
    <header class="sticky top-0 z-30 h-12 topbar-glass border-b border-green-100/40 flex items-center justify-between px-3 lg:px-4">
        <div class="flex items-center gap-2">
            <button @click="sideOpen=!sideOpen" 
                    class="w-7 h-7 rounded-lg bg-white border border-gray-200/60 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:border-sage/40 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
            </button>
            <div class="hidden sm:flex items-center gap-1 text-xs text-gray-400">
                <a href="{{ url('/') }}" class="hover:text-sage transition">Beranda</a>
                <span>/</span>
                <span class="text-gray-700 font-semibold">@yield('page_title','Dashboard')</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="hidden lg:flex relative">
                <svg class="absolute left-2.5 top-1.5 w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari..." class="h-7 w-40 bg-gray-100/80 border border-gray-200/60 rounded-lg pl-7 pr-2 text-xs focus:outline-none focus:ring-2 focus:ring-sage/30 focus:border-sage/50 transition">
            </div>
            <div class="w-7 h-7 rounded-lg bg-white border border-gray-200/60 flex items-center justify-center text-gray-400 relative shadow-sm hover:border-sage/40 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs" style="background:rgba(114,191,119,.2);color:#3fa348">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 p-3 lg:p-4">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm font-semibold fade-up">
                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error') || $errors->any())
            <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-semibold fade-up">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

{{-- BOTTOM NAV mobile --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bottom-nav">
    <div class="flex items-center justify-around px-1 py-2 max-w-md mx-auto">
        @if(Auth::user()->role !== 'admin')
            {{-- Home --}}
            <a href="{{ route('buyer.dashboard') }}" class="bottom-nav-item {{ Request::is('buyer/dashboard') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="bottom-nav-label">Home</span>
            </a>
            
            {{-- Shop --}}
            <a href="{{ route('buyer.products') }}" class="bottom-nav-item {{ Request::is('products*') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="bottom-nav-label">Belanja</span>
            </a>
            
            {{-- Cart --}}
            <a href="{{ route('buyer.cart') }}" class="bottom-nav-item relative {{ Request::is('cart*') ? 'active' : 'text-gray-500' }}">
                <div class="relative">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
                    @php $cc=Auth::user()->carts->count(); @endphp
                    @if($cc>0)<span class="bottom-nav-badge">{{$cc>9?'9+':$cc}}</span>@endif
                </div>
                <span class="bottom-nav-label">Keranjang</span>
            </a>
            
            {{-- Orders --}}
            <a href="{{ route('buyer.orders') }}" class="bottom-nav-item {{ Request::is('orders*') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="bottom-nav-label">Pesanan</span>
            </a>
            
            {{-- Profile/More --}}
            <a href="{{ route('profile') }}" class="bottom-nav-item {{ Request::is('profile*') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="bottom-nav-label">Profil</span>
            </a>
            
        @else
            {{-- Admin navigation --}}
            <a href="/admin/dashboard" class="bottom-nav-item active">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="bottom-nav-label">Panel</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="bottom-nav-item text-gray-500">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="bottom-nav-label">Users</span>
            </a>
            <a href="{{ route('admin.verifications.index') }}" class="bottom-nav-item text-gray-500">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="bottom-nav-label">Verifikasi</span>
            </a>
            <a href="{{ route('profile') }}" class="bottom-nav-item text-gray-500">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="bottom-nav-label">Profil</span>
            </a>
        @endif
    </div>
</nav>

{{-- CHAT MODAL (buyer) --}}
@if(Auth::check() && Auth::user()->role !== 'admin')
<div x-show="chatModal" @click="chatModal=false" x-cloak class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm" style="display:none">
    <div @click.stop class="absolute right-0 top-0 w-full sm:w-80 h-full bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between p-3 lg:p-4 border-b border-gray-100">
            <h3 class="font-black text-gray-900 text-sm lg:text-base">Chat Seller</h3>
            <button @click="chatModal=false" class="w-7 lg:w-8 h-7 lg:h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
                <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-2 lg:p-3 space-y-1.5 lg:space-y-2">
            @php
                $chats = \App\Models\Chat::where('buyer_id', Auth::id())->with(['order.product','order.store.user','messages'])->latest()->get();
            @endphp
            @forelse($chats as $chat)
                @php
                    $unread = $chat->messages()->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
                    $last   = $chat->messages()->latest()->first();
                @endphp
                <a href="{{ route('chat.show', $chat->order) }}" @click="chatModal=false" class="flex items-center gap-2 lg:gap-3 p-2.5 lg:p-3 rounded-lg lg:rounded-xl hover:bg-gray-50 border border-gray-100 transition">
                    <div class="w-7 lg:w-9 h-7 lg:h-9 rounded-lg lg:rounded-xl flex items-center justify-center font-bold text-xs lg:text-sm flex-shrink-0" style="background:rgba(114,191,119,.15);color:#3fa348">
                        {{ strtoupper(substr($chat->order->store->user->name??'?',0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs lg:text-xs text-gray-900 truncate">{{ $chat->order->store->user->name ?? '-' }}</p>
                        <p class="text-[10px] lg:text-[11px] text-gray-400 truncate">{{ $last->message ?? 'Mulai chat' }}</p>
                    </div>
                    @if($unread>0)<span class="bg-sage text-white text-[8px] lg:text-[9px] font-black px-1 lg:px-1.5 py-0.5 rounded-full flex-shrink-0">{{$unread}}</span>@endif
                </a>
            @empty
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <svg class="w-8 lg:w-10 h-8 lg:h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-xs lg:text-sm font-medium">Belum ada percakapan</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- FLOATING CHAT BUTTON (mobile only) --}}
@if(Auth::user()->role !== 'admin')
<button @click="chatModal=true" class="lg:hidden fixed bottom-20 right-4 z-40 w-12 h-12 rounded-full shadow-xl flex items-center justify-center text-white transition-all duration-300 hover:scale-110 active:scale-95 floating-chat" style="background:linear-gradient(135deg,#72bf77,#4db85a)">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    @php $unreadTotal = \App\Models\Message::whereHas('chat', fn($q) => $q->where('buyer_id', Auth::id()))->where('sender_id','!=',Auth::id())->where('is_read',false)->count(); @endphp
    @if($unreadTotal > 0)
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">{{ $unreadTotal > 9 ? '9+' : $unreadTotal }}</span>
    @endif
</button>
@endif
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
    const base={background:'#fff',color:'#111827',customClass:{popup:'rounded-2xl shadow-2xl',title:'text-lg font-black',htmlContainer:'text-sm text-gray-500',confirmButton:'rounded-xl px-5 py-2.5 font-bold text-sm',cancelButton:'rounded-xl px-5 py-2.5 font-bold text-sm'},buttonsStyling:false};
    window.arradeaPopup={
        _fire(c){return typeof Swal!=='undefined'?Swal.fire({...base,...c}):null},
        success(msg,title){return this._fire({icon:'success',iconColor:'#72bf77',title:title||'✅ Berhasil',text:msg,confirmButtonColor:'#72bf77'})},
        error(msg,title){return this._fire({icon:'error',iconColor:'#dc2626',title:title||'❌ Gagal',text:msg,confirmButtonColor:'#dc2626'})},
        confirm(msg,opts={}){if(typeof Swal==='undefined')return Promise.resolve(false);return Swal.fire({...base,icon:'warning',iconColor:'#f59e0b',title:opts.title||'Konfirmasi',text:msg||'Lanjutkan?',showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, lanjut',cancelButtonText:'Batal',confirmButtonColor:opts.confirmColor||'#72bf77',reverseButtons:true}).then(r=>r.isConfirmed)},
        danger(msg,opts={}){if(typeof Swal==='undefined')return Promise.resolve(false);return Swal.fire({...base,icon:'warning',iconColor:'#dc2626',title:opts.title||'⚠️ Hapus?',text:msg,showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626',reverseButtons:true}).then(r=>r.isConfirmed)}
    };
    window.confirmSubmit=function(e,msg){e&&e.preventDefault();const f=e&&e.target;if(!f)return false;window.arradeaPopup.danger(msg).then(ok=>{if(ok)f.submit()});return false};
    
    // Logout confirmation
    window.confirmLogout=function(e){
        e.preventDefault();
        Swal.fire({
            ...base,
            icon:'question',
            iconColor:'#f59e0b',
            title:'Yakin ingin keluar?',
            text:'Anda akan keluar dari akun ini',
            showCancelButton:true,
            confirmButtonText:'Ya, Keluar',
            cancelButtonText:'Batal',
            confirmButtonColor:'#dc2626',
            cancelButtonColor:'#6b7280',
            reverseButtons:true
        }).then((result)=>{
            if(result.isConfirmed){
                document.getElementById('logoutForm').submit();
            }
        });
    };
})();
</script>
@stack('scripts')
</body>
</html>
