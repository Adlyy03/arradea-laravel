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
    <style>
        [x-cloak]{display:none!important}
        *{-webkit-font-smoothing:antialiased}
        ::-webkit-scrollbar{width:4px;height:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#2d4a30;border-radius:10px}
        .sidebar-item{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:12px;transition:all .2s;font-weight:600;font-size:.8125rem;color:#9db89f;cursor:pointer;text-decoration:none}
        .sidebar-item:hover{background:rgba(114,191,119,.12);color:#b3e6b8}
        .sidebar-item.active{background:rgba(114,191,119,.18);color:#72bf77}
        .sidebar-item.active svg{color:#72bf77}
        .sidebar-item svg{flex-shrink:0;width:18px;height:18px;transition:color .2s}
        .topbar-glass{background:rgba(247,250,247,.9);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px)}
        .stat-card{background:rgba(255,255,255,.8);border:1px solid rgba(114,191,119,.15);border-radius:16px;padding:20px;transition:all .25s;cursor:default}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(114,191,119,.15);border-color:rgba(114,191,119,.35)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .fade-up{animation:fadeUp .35s ease both}
        .badge-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0}
    </style>
    @stack('styles')
</head>
<body class="bg-[#f2f5f2] font-sans text-gray-900 overflow-x-hidden" x-data="{ sideOpen: window.innerWidth >= 1024, chatModal: false }">

{{-- SIDEBAR OVERLAY mobile --}}
<div x-show="sideOpen && window.innerWidth < 1024" @click="sideOpen=false" x-cloak class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"></div>

{{-- SIDEBAR --}}
<aside :class="sideOpen ? 'w-[220px]' : 'w-[60px] lg:w-[60px]'"
    class="fixed top-0 left-0 h-screen z-50 flex flex-col transition-all duration-300 overflow-hidden"
    style="background:linear-gradient(160deg,#0f1a11 0%,#152218 60%,#0f1a11 100%);border-right:1px solid rgba(114,191,119,.12)">

    {{-- Logo area --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-white/5">
        <div class="w-8 h-8 rounded-xl bg-[#72bf77] flex items-center justify-center shadow-lg shadow-green-900/50 flex-shrink-0">
            <span class="text-white font-black text-sm">A</span>
        </div>
        <span x-show="sideOpen" x-cloak class="text-white font-black text-lg tracking-tight truncate" style="text-shadow:0 1px 8px rgba(114,191,119,.3)">Arradea<span style="color:#72bf77">.</span></span>
    </div>

    {{-- User info --}}
    <div x-show="sideOpen" x-cloak class="mx-3 mt-4 mb-2 p-3 rounded-2xl" style="background:rgba(114,191,119,.08);border:1px solid rgba(114,191,119,.12)">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0" style="background:rgba(114,191,119,.25);color:#72bf77">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-white text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] uppercase tracking-widest font-semibold truncate" style="color:#72bf77">
                    @if(Auth::user()->role==='admin') Admin @elseif(Auth::user()->is_seller) Seller @else Buyer @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-2 py-1 space-y-0.5">
        @if(Auth::user()->role === 'admin')
            @include('components.sidebar.admin')
        @else
            @include('components.sidebar.buyer')
            @if(Auth::user()->is_seller)
                <div class="pt-3 mt-2" style="border-top:1px solid rgba(114,191,119,.1)">
                    <p x-show="sideOpen" x-cloak class="text-[9px] uppercase font-black tracking-widest px-3 pb-2" style="color:#4a7a4e">Menu Seller</p>
                    @include('components.sidebar.seller')
                </div>
            @endif
        @endif
    </nav>

    {{-- Logout --}}
    <div class="p-3 border-t" style="border-color:rgba(114,191,119,.1)">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item w-full hover:!bg-red-900/30 hover:!text-red-400 group">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span x-show="sideOpen" x-cloak>Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<div :style="sideOpen ? 'margin-left:220px' : 'margin-left:60px'" class="min-h-screen flex flex-col transition-all duration-300 lg:block" style="margin-left:60px" x-init="$watch('sideOpen', v => {})">

    {{-- TOPBAR --}}
    <header class="sticky top-0 z-30 h-14 topbar-glass border-b border-green-100/40 flex items-center justify-between px-4 lg:px-6">
        <div class="flex items-center gap-3">
            <button @click="sideOpen=!sideOpen" class="w-8 h-8 rounded-xl bg-white border border-gray-200/60 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:border-sage/40 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
            </button>
            <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-400">
                <a href="{{ url('/') }}" class="hover:text-sage transition">Beranda</a>
                <span>/</span>
                <span class="text-gray-700 font-semibold">@yield('page_title','Dashboard')</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="hidden lg:flex relative">
                <svg class="absolute left-3 top-2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari..." class="h-8 w-48 bg-gray-100/80 border border-gray-200/60 rounded-lg pl-8 pr-3 text-xs focus:outline-none focus:ring-2 focus:ring-sage/30 focus:border-sage/50 transition">
            </div>
            <div class="w-8 h-8 rounded-xl bg-white border border-gray-200/60 flex items-center justify-center text-gray-400 relative shadow-sm hover:border-sage/40 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm" style="background:rgba(114,191,119,.2);color:#3fa348">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 p-4 lg:p-6">
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
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-xl">
    <div class="flex items-center justify-around px-2 py-2 max-w-sm mx-auto">
        @if(Auth::user()->role !== 'admin')
            <a href="{{ route('buyer.dashboard') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 {{ Request::is('buyer/dashboard') ? 'text-sage' : 'text-gray-400' }} hover:text-sage transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[9px] font-bold">Home</span>
            </a>
            <a href="{{ route('buyer.products') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 {{ Request::is('products*') ? 'text-sage' : 'text-gray-400' }} hover:text-sage transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="text-[9px] font-bold">Belanja</span>
            </a>
            <a href="{{ route('buyer.cart') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 relative {{ Request::is('cart*') ? 'text-sage' : 'text-gray-400' }} hover:text-sage transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
                @php $cc=Auth::user()->carts->count(); @endphp
                @if($cc>0)<span class="absolute top-0 right-3 bg-sage text-white text-[8px] font-black w-4 h-4 rounded-full flex items-center justify-center">{{$cc>9?'9+':$cc}}</span>@endif
                <span class="text-[9px] font-bold">Keranjang</span>
            </a>
            <a href="{{ route('buyer.orders') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 {{ Request::is('orders*') ? 'text-sage' : 'text-gray-400' }} hover:text-sage transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[9px] font-bold">Pesanan</span>
            </a>
            <button @click="chatModal=true" class="flex flex-col items-center gap-0.5 flex-1 py-1 text-gray-400 hover:text-sage transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="text-[9px] font-bold">Chat</span>
            </button>
        @else
            <a href="/admin/dashboard" class="flex flex-col items-center gap-0.5 flex-1 py-1 text-sage"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg><span class="text-[9px] font-bold">Panel</span></a>
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 text-gray-400 hover:text-sage transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg><span class="text-[9px] font-bold">Users</span></a>
            <a href="{{ route('admin.verifications.index') }}" class="flex flex-col items-center gap-0.5 flex-1 py-1 text-gray-400 hover:text-sage transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span class="text-[9px] font-bold">Verifikasi</span></a>
        @endif
    </div>
</nav>

{{-- CHAT MODAL (buyer) --}}
@if(Auth::check() && Auth::user()->role !== 'admin')
<div x-show="chatModal" @click="chatModal=false" x-cloak class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm" style="display:none">
    <div @click.stop class="absolute right-0 top-0 w-full sm:w-80 h-full bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h3 class="font-black text-gray-900">Chat Seller</h3>
            <button @click="chatModal=false" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            @php
                $chats = \App\Models\Chat::where('buyer_id', Auth::id())->with(['order.product','order.store.user','messages'])->latest()->get();
            @endphp
            @forelse($chats as $chat)
                @php
                    $unread = $chat->messages()->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
                    $last   = $chat->messages()->latest()->first();
                @endphp
                <a href="{{ route('chat.show', $chat->order) }}" @click="chatModal=false" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-gray-100 transition">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0" style="background:rgba(114,191,119,.15);color:#3fa348">
                        {{ strtoupper(substr($chat->order->store->user->name??'?',0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs text-gray-900 truncate">{{ $chat->order->store->user->name ?? '-' }}</p>
                        <p class="text-[11px] text-gray-400 truncate">{{ $last->message ?? 'Mulai chat' }}</p>
                    </div>
                    @if($unread>0)<span class="bg-sage text-white text-[9px] font-black px-1.5 py-0.5 rounded-full flex-shrink-0">{{$unread}}</span>@endif
                </a>
            @empty
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-sm font-medium">Belum ada percakapan</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
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
})();
</script>
@stack('scripts')
</body>
</html>
