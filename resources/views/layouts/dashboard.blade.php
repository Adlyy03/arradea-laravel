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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
        
        /* Hide floating chat when sidebar is open on mobile */
        @media(max-width:1023px){
            .floating-chat{
                transition:opacity .3s ease, transform .3s ease;
                opacity:1;
                pointer-events:auto;
                transform:scale(1) translateY(0);
            }
            .sidebar-open .floating-chat{
                opacity:0;
                pointer-events:none;
                transform:scale(0.8) translateY(20px);
            }
            
            /* Adjust main content for mobile without sidebar */
            .min-h-screen {
                margin-left: 0 !important;
                padding-bottom: calc(84px + env(safe-area-inset-bottom)) !important;
            }
        }

        .mobile-content-shell{
            width:100%;
            max-width:1200px;
            margin-inline:auto;
        }
        
        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            transition: opacity 0.3s ease;
        }

        /* ── Sidebar Core ─────────────────────────────── */
        .sb-item{
            display:flex;align-items:center;gap:12px;
            padding:8px 10px;
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
        
        /* Mobile topbar optimizations */
        @media(max-width:1023px){
            .topbar-glass{height:56px !important;padding:0 12px !important}
            .topbar-glass h1,.topbar-glass .text-xl,.topbar-glass .text-2xl{font-size:15px !important;font-weight:700 !important}
        }

        /* Stat card */
        .stat-card{background:rgba(255,255,255,.8);border:1px solid rgba(114,191,119,.15);border-radius:16px;padding:20px;transition:all .25s;cursor:default}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(114,191,119,.15);border-color:rgba(114,191,119,.35)}

        @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .fade-up{animation:fadeUp .35s ease both}
        .badge-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0}

        /* Mobile optimizations - Ultra Compact */
        @media(max-width:1023px){
            .sb-section-label{padding-top:8px;padding-bottom:4px;font-size:.6rem}
            .sb-item{padding:6px 8px;font-size:.75rem;gap:8px;border-radius:10px;margin:2px 0}
            .sb-icon{width:28px;height:28px;border-radius:8px}
            .sb-icon svg{width:14px;height:14px}
            .sb-badge{font-size:.6rem;padding:2px 6px}
            .sb-label{font-size:.75rem}
            .sb-status-chip{margin:6px 4px 4px;padding:8px 10px;border-radius:8px;gap:6px}
            .sb-chip-title{font-size:.6rem}
            .sb-chip-desc{font-size:.6rem;margin-top:2px}
        }
        
        /* Mode Switch Tabs - Mobile Only */
        .mode-switch-container{
            padding:8px;
            background:rgba(255,255,255,.08);
            border-radius:10px;
            margin:8px 8px 12px;
        }
        .mode-switch{
            display:flex;
            gap:4px;
            background:rgba(0,0,0,.15);
            padding:3px;
            border-radius:8px;
        }
        .mode-tab{
            flex:1;
            padding:6px 8px;
            border-radius:6px;
            font-size:.7rem;
            font-weight:700;
            text-align:center;
            color:rgba(255,255,255,.6);
            cursor:pointer;
            transition:all .2s;
            text-transform:uppercase;
            letter-spacing:.03em;
        }
        .mode-tab.active{
            background:rgba(255,255,255,.25);
            color:white;
            box-shadow:0 2px 8px rgba(0,0,0,.15);
        }
        
        /* Role Badge Styles */
        .sb-role-badge{
            display:inline-flex;
            align-items:center;
            gap:3px;
            padding:3px 6px;
            border-radius:5px;
            font-size:.6rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.03em;
        }
        .sb-role-buyer{
            background:rgba(59,130,246,.25);
            color:#93c5fd;
            border:1px solid rgba(59,130,246,.3);
        }
        .sb-role-seller{
            background:rgba(245,158,11,.25);
            color:#fbbf24;
            border:1px solid rgba(245,158,11,.3);
        }
        .sb-role-verified{
            display:inline-flex;
            align-items:center;
            padding:2px 5px;
            border-radius:4px;
            font-size:.55rem;
            font-weight:700;
            background:rgba(34,197,94,.25);
            color:#86efac;
            border:1px solid rgba(34,197,94,.3);
        }
        .sb-role-pending{
            display:inline-flex;
            align-items:center;
            padding:2px 5px;
            border-radius:4px;
            font-size:.55rem;
            font-weight:700;
            background:rgba(245,158,11,.25);
            color:#fbbf24;
            border:1px solid rgba(245,158,11,.3);
        }
        .sb-role-badge-count{
            padding:2px 5px;
            border-radius:4px;
            font-size:.55rem;
            font-weight:700;
            background:rgba(255,255,255,.15);
            color:rgba(255,255,255,.8);
        }
        
        /* Quick Stats Cards - Mobile */
        .sb-stat-card{
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.18);
            border-radius:8px;
            padding:6px;
            text-align:center;
            transition:all .2s;
            text-decoration:none;
            display:block;
        }
        .sb-stat-card:hover,.sb-stat-card.sb-stat-active{
            background:rgba(255,255,255,.2);
            border-color:rgba(255,255,255,.35);
            transform:translateY(-1px);
        }
        .sb-stat-num{
            font-size:1.1rem;
            font-weight:800;
            color:white;
            line-height:1;
            position:relative;
            display:inline-block;
        }
        .sb-stat-lbl{
            font-size:.6rem;
            font-weight:600;
            color:rgba(255,255,255,.65);
            margin-top:3px;
            text-transform:uppercase;
            letter-spacing:.03em;
        }
        .sb-stat-dot{
            position:absolute;
            top:-2px;
            right:-8px;
            width:6px;
            height:6px;
            border-radius:50%;
            background:#86efac;
            animation:pulse 2s infinite;
        }
        .sb-stat-dot-amber{
            background:#fbbf24;
        }
        .sb-stat-dot-red{
            background:#f87171;
        }
        @keyframes pulse{
            0%,100%{opacity:1;transform:scale(1)}
            50%{opacity:.6;transform:scale(1.1)}
        }
        
        /* Divider */
        .sb-divider{
            padding:6px 6px 2px;
            font-size:.5rem;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
            color:rgba(255,255,255,.35);
            border-top:1px solid rgba(255,255,255,.08);
            margin-top:6px;
        }
        
        /* Bottom navigation styles - Ultra Compact */
        .bottom-nav{
            background:rgba(255,255,255,0.96);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            border-top:1px solid rgba(114,191,119,0.15);
            box-shadow:0 -2px 12px rgba(0,0,0,0.08);
            min-height:60px;
            padding:8px 0 calc(8px + env(safe-area-inset-bottom));
        }
        .bottom-nav-item{
            display:flex;flex-direction:column;align-items:center;gap:2px;
            padding:4px 2px;border-radius:8px;transition:all .2s;
            text-decoration:none;min-width:0;flex:1;
        }
        .bottom-nav-item:hover,.bottom-nav-item.active{
            background:rgba(114,191,119,0.12);
            color:#72bf77;
        }
        .bottom-nav-icon{width:20px;height:20px;flex-shrink:0}
        .bottom-nav-label{font-size:10px;font-weight:600;line-height:1.2;text-align:center}
        .bottom-nav-badge{
            position:absolute;top:-2px;right:-2px;
            background:#72bf77;color:white;
            font-size:8px;font-weight:800;
            width:14px;height:14px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid white;
        }
        
        /* Floating button animation - Compact */
        .floating-chat{
            animation:float 3s ease-in-out infinite;
            width:44px;height:44px;
            box-shadow:0 4px 12px rgba(114,191,119,0.3);
        }
        @keyframes float{
            0%,100%{transform:translateY(0px)}
            50%{transform:translateY(-6px)}
        }
        
        @media(max-width:1023px){
            .floating-chat{
                width:44px !important;
                height:44px !important;
                bottom:calc(84px + env(safe-area-inset-bottom)) !important;
                right:calc(14px + env(safe-area-inset-right)) !important;
            }
            .floating-chat svg{
                width:20px !important;
                height:20px !important;
            }

            .floating-support{
                bottom:calc(146px + env(safe-area-inset-bottom)) !important;
                right:calc(14px + env(safe-area-inset-right)) !important;
            }
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
      :class="(isMobile && sideOpen) ? 'sidebar-open' : ''"
      @resize.window="isMobile = window.innerWidth < 1024; if (!isMobile) sideOpen = true; else sideOpen = false;">

{{-- Overlay for mobile sidebar --}}
<div x-show="sideOpen && isMobile" 
     x-cloak
     @click="sideOpen = false"
     class="sidebar-overlay"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

{{-- SIDEBAR - HIJAU GELAP GRADASI --}}
<aside 
    x-cloak
    class="fixed top-0 left-0 h-screen flex flex-col overflow-hidden transition-all duration-300 ease-out shadow-2xl"
    :style="isMobile ? (sideOpen ? 'width:260px; transform:translateX(0); z-index:50; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)' : 'width:260px; transform:translateX(-100%); z-index:50; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)') : (sideOpen ? 'width:240px; transform:translateX(0); z-index:30; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)' : 'width:70px; transform:translateX(0); z-index:30; background:linear-gradient(180deg, #1e5128 0%, #2d6a3e 50%, #1e5128 100%)')"
    style="border-right:2px solid #72bf77">

    {{-- Logo area --}}
    <div class="flex items-center justify-between px-3 lg:px-4 h-[56px] lg:h-[60px] flex-shrink-0 border-b border-white/10">
        <div class="flex items-center gap-2 lg:gap-3">
            <img src="/icons/logo-arradea.png" alt="Arradea" class="w-9 lg:w-10 h-9 lg:h-10 rounded-xl flex-shrink-0 object-cover shadow-lg" style="background:white;">
            <div x-show="sideOpen" x-cloak class="overflow-hidden">
                <span class="text-white font-black text-base lg:text-base tracking-tight block">Arradea</span>
                <span class="text-[10px] lg:text-[10px] uppercase tracking-wider font-semibold text-white/70">Marketplace</span>
            </div>
        </div>
        {{-- Close button for mobile --}}
        <button @click="sideOpen=false" 
                x-show="sideOpen && isMobile" 
                class="lg:hidden w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- User info card --}}
    <div class="px-3 lg:px-3 pt-3 lg:pt-4 pb-2 lg:pb-2 flex-shrink-0">
        <div x-show="sideOpen" x-cloak
            class="flex items-center gap-2 lg:gap-3 p-2 lg:p-3 rounded-xl bg-white/10 border border-white/20 backdrop-blur-sm">
            <div class="w-9 lg:w-10 h-9 lg:h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-xs lg:text-sm bg-white shadow-md" style="color:#1e5128">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
            <div class="overflow-hidden flex-1 min-w-0">
                <p class="text-white text-sm lg:text-sm font-bold truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] lg:text-[10px] uppercase tracking-wide font-semibold truncate mt-1 lg:mt-1 text-white/60">
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
            <div class="w-11 h-11 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-sm bg-white shadow-md" style="color:#1e5128">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-2 space-y-1">
        @if(Auth::user()->role === 'admin')
            @include('components.sidebar.admin')
        @else
            @php
                $activeMode = Auth::user()->getActiveMode();
            @endphp
            
            @if($activeMode === 'seller' && Auth::user()->canSwitchToSellerMode())
                {{-- Seller Mode: Show only seller menu --}}
                @include('components.sidebar.seller')
            @else
                {{-- Buyer Mode: Show only buyer menu --}}
                @include('components.sidebar.buyer')
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
     :class="isMobile ? 'ml-0 pb-20' : (sideOpen ? 'ml-[240px]' : 'ml-[70px]')">

    {{-- TOPBAR --}}
    <header class="sticky top-0 z-30 h-14 lg:h-12 topbar-glass border-b border-green-100/40 flex items-center justify-between px-3 lg:px-4">
        <div class="flex items-center gap-2">
            {{-- Hamburger: desktop only --}}
            <button @click="sideOpen=!sideOpen" 
                    class="hidden lg:flex w-7 h-7 rounded-lg bg-white border border-gray-200/60 items-center justify-center text-gray-500 hover:bg-gray-50 hover:border-sage/40 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
            </button>
            {{-- Breadcrumb desktop / Page title mobile --}}
            <div class="flex items-center gap-1 text-xs text-gray-400">
                <a href="{{ url('/') }}" class="hidden lg:inline hover:text-sage transition">Beranda</a>
                <span class="hidden lg:inline">/</span>
                <span class="text-gray-800 font-bold text-sm lg:text-xs lg:font-semibold lg:text-gray-700">@yield('page_title','Dashboard')</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 lg:w-7 lg:h-7 rounded-lg flex items-center justify-center font-bold text-xs" style="background:rgba(114,191,119,.2);color:#3fa348">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
            {{-- PWA install button (explicit) --}}
            <button id="pwa-install-btn" type="button" class="hidden lg:inline-flex items-center gap-2 px-3 py-2 rounded-full bg-black text-white text-sm font-semibold ml-3 transition" aria-hidden="true">Tambahkan ke Beranda</button>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 px-4 sm:px-5 lg:p-4 pt-3 pb-4 lg:pb-4">
        <div class="mobile-content-shell">
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
        </div>
    </main>
</div>

{{-- BOTTOM NAV mobile --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bottom-nav">
    <div class="flex items-center justify-around px-3 pt-1.5 pb-0.5 max-w-md sm:max-w-lg mx-auto">
        @if(Auth::user()->role !== 'admin')
            @php
                $activeMode = Auth::user()->getActiveMode();
                $isBuyerMode = $activeMode === 'buyer';
                $isSellerMode = $activeMode === 'seller' && Auth::user()->canSwitchToSellerMode();
            @endphp
            
            @if($isSellerMode)
                {{-- SELLER MODE NAVIGATION --}}
                
                {{-- Dashboard --}}
                <a href="{{ route('seller.dashboard') }}" class="bottom-nav-item {{ Request::is('seller/dashboard') ? 'active' : 'text-gray-500' }}">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="bottom-nav-label">Dashboard</span>
                </a>
                
                {{-- Products --}}
                <a href="{{ route('seller.products') }}" class="bottom-nav-item {{ Request::is('seller/products*') ? 'active' : 'text-gray-500' }}">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
                    <span class="bottom-nav-label">Produk</span>
                </a>
                
                {{-- Orders --}}
                <a href="{{ route('seller.orders') }}" class="bottom-nav-item relative {{ Request::is('seller/orders*') ? 'active' : 'text-gray-500' }}">
                    <div class="relative">
                        <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @php $sellerPendingOrders = Auth::user()->store ? Auth::user()->store->orders()->where('status','pending')->count() : 0; @endphp
                        @if($sellerPendingOrders > 0)<span class="bottom-nav-badge">{{ $sellerPendingOrders > 9 ? '9+' : $sellerPendingOrders }}</span>@endif
                    </div>
                    <span class="bottom-nav-label">Pesanan</span>
                </a>
                
                {{-- Messages --}}
                <a href="{{ route('seller.messages') }}" class="bottom-nav-item relative {{ Request::is('seller/messages*') ? 'active' : 'text-gray-500' }}">
                    <div class="relative">
                        <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @php $sellerUnread = \App\Models\Message::whereHas('chat', fn($q) => $q->where('seller_id', Auth::id()))->where('sender_id','!=',Auth::id())->where('is_read',false)->count(); @endphp
                        @if($sellerUnread > 0)<span class="bottom-nav-badge">{{ $sellerUnread > 9 ? '9+' : $sellerUnread }}</span>@endif
                    </div>
                    <span class="bottom-nav-label">Pesan</span>
                </a>
                
                {{-- Profile --}}
                <a href="{{ route('profile') }}" class="bottom-nav-item {{ Request::is('profile*') ? 'active' : 'text-gray-500' }}">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="bottom-nav-label">Profil</span>
                </a>
                
            @else
                {{-- BUYER MODE NAVIGATION --}}
                
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
                <a href="{{ route('buyer.orders') }}" class="bottom-nav-item {{ Request::is('buyer/orders*','orders*') ? 'active' : 'text-gray-500' }}">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="bottom-nav-label">Pesanan</span>
                </a>
                
                {{-- Profile --}}
                <a href="{{ route('profile') }}" class="bottom-nav-item {{ Request::is('profile*') ? 'active' : 'text-gray-500' }}">
                    <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="bottom-nav-label">Profil</span>
                </a>
            @endif
            
        @else
            {{-- ADMIN NAVIGATION --}}
            <a href="/admin/dashboard" class="bottom-nav-item {{ Request::is('admin/dashboard') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="bottom-nav-label">Panel</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="bottom-nav-item {{ Request::is('admin/users*') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="bottom-nav-label">Users</span>
            </a>
            <a href="{{ route('admin.verifications.index') }}" class="bottom-nav-item {{ Request::is('admin/verifications*') ? 'active' : 'text-gray-500' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="bottom-nav-label">Verifikasi</span>
            </a>
            <a href="{{ route('profile') }}" class="bottom-nav-item {{ Request::is('profile*') ? 'active' : 'text-gray-500' }}">
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

{{-- Floating Customer Service Button (Non-Admin Only) --}}
@if(Auth::check() && Auth::user()->role !== 'admin')
<button @click="$refs.complaintModal.showModal()" 
    class="floating-support fixed bottom-[136px] lg:bottom-6 right-4 lg:right-6 z-40 w-12 h-12 lg:w-14 lg:h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all duration-300 hover:scale-110 active:scale-95"
        style="background:linear-gradient(135deg,#72bf77,#4db85a)">
    <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
</button>

{{-- Complaint Modal --}}
<dialog x-ref="complaintModal" class="rounded-2xl p-0 backdrop:bg-black/50 max-w-md w-full">
    <div class="bg-white rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h3 class="font-black text-gray-900">Customer Service</h3>
            <button @click="$refs.complaintModal.close()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('complaints.store') }}" method="POST" class="p-4 space-y-4" onsubmit="return handleComplaintSubmit(event)">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Subjek</label>
                <input type="text" name="subject" required
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-sage/30 text-sm"
                    placeholder="Contoh: Produk tidak sesuai">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pesan Keluhan</label>
                <textarea name="message" rows="4" required
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-sage/30 text-sm"
                    placeholder="Jelaskan keluhan Anda..."></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                    Kirim
                </button>
                <button type="button" @click="$refs.complaintModal.close()" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
function handleComplaintSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            document.querySelector('dialog').close();
            
            // Show success popup
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    iconColor: '#72bf77',
                    title: '✅ Keluhan Terkirim',
                    text: 'Keluhan akan dilanjutkan oleh admin',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#72bf77',
                    background: '#fff',
                    color: '#111827',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl',
                        title: 'text-lg font-black',
                        htmlContainer: 'text-sm text-gray-500',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-sm'
                    },
                    buttonsStyling: false
                });
            }
            
            // Reset form
            form.reset();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan, silakan coba lagi',
                confirmButtonColor: '#dc2626'
            });
        }
    });
    
    return false;
}
</script>
@endif

@stack('scripts')

<script defer src="/js/pwa.js"></script>
</body>
</html>
