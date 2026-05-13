{{-- Buyer Sidebar Nav --}}
@php
    $cartCount        = Auth::user()->carts->count();
    $pendingOrderCount = Auth::user()->orders()->whereIn('status',['pending','processing'])->count();
    $unreadMsgCount   = \App\Models\Message::whereHas('chat', fn($q) => $q->where('buyer_id', Auth::id()))
                            ->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
@endphp

{{-- ============================================================
     DESKTOP sidebar: tampilkan semua menu buyer
     ============================================================ --}}
<div class="hidden lg:block">
    <a href="{{ route('buyer.dashboard') }}" class="sb-item {{ Request::is('buyer/dashboard') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Dashboard</span>
    </a>

    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Belanja</span></div>

    <a href="{{ route('buyer.products') }}" class="sb-item {{ Request::is('products*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Produk</span>
    </a>

    <a href="{{ route('buyer.cart') }}" class="sb-item {{ Request::is('cart*') ? 'sb-active' : '' }}">
        <span class="sb-icon" style="position:relative">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
            @if($cartCount > 0)
                <span class="sb-icon-dot"></span>
            @endif
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Keranjang</span>
        @if($cartCount > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-green">{{ $cartCount }}</span>
        @endif
    </a>

    <a href="{{ route('buyer.orders') }}" class="sb-item {{ Request::is('orders*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Pesanan</span>
        @if($pendingOrderCount > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-amber">{{ $pendingOrderCount }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-amber"></span>
        @endif
    </a>

    @php
        $qrisPaymentCount = Auth::user()->orders()
            ->where('payment_method', 'qris')
            ->whereIn('payment_status', ['waiting_confirmation', 'rejected'])
            ->count();
    @endphp
    <a href="{{ route('buyer.payments') }}" class="sb-item {{ Request::is('buyer/payments*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Bukti Pembayaran</span>
        @if($qrisPaymentCount > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-blue">{{ $qrisPaymentCount }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-blue"></span>
        @endif
    </a>

    <button @click="chatModal=true" class="sb-item w-full text-left">
        <span class="sb-icon" style="position:relative">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            @if($unreadMsgCount > 0)
                <span class="sb-icon-dot sb-icon-dot-red"></span>
            @endif
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Chat</span>
        @if($unreadMsgCount > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-red">{{ $unreadMsgCount }}</span>
        @endif
    </button>

    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Akun</span></div>

    <a href="{{ route('profile') }}" class="sb-item {{ Request::is('profile*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Profil & Akun</span>
    </a>

    <a href="{{ route('profile') }}#account-security" class="sb-item {{ Request::is('profile*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 11c0-1.657 1.343-3 3-3h1V7a4 4 0 10-8 0v1h1c1.657 0 3 1.343 3 3v1zm0 0v2m-6 6h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Keamanan Akun</span>
    </a>
</div>

<div class="lg:hidden mt-2 space-y-4 px-2">
    {{-- Buyer Info Card --}}
    <div class="p-3 bg-white/10 rounded-xl border border-white/20 backdrop-blur-md shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    Mode Pembeli
                </span>
            </div>
            @if($cartCount > 0)
                <span class="text-[10px] font-bold text-white/70">{{ $cartCount }} Item di Keranjang</span>
            @endif
        </div>
        
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('buyer.cart') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all">
                <span class="text-xl font-black text-white mb-1">{{ $cartCount }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Keranjang</span>
            </a>
            <a href="{{ route('buyer.orders') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all relative">
                @if($pendingOrderCount > 0)
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-amber-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(251,191,36,0.8)]"></span>
                @endif
                <span class="text-xl font-black text-white mb-1">{{ $pendingOrderCount }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Pending</span>
            </a>
        </div>
    </div>

    {{-- Menu Actions --}}
    <div class="space-y-1.5">
        @php
            $qrisPaymentCount = Auth::user()->orders()
                ->where('payment_method', 'qris')
                ->whereIn('payment_status', ['waiting_confirmation', 'rejected'])
                ->count();
        @endphp
        <a href="{{ route('buyer.payments') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white relative transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    @if($qrisPaymentCount > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 border-2 border-[#1e5128] rounded-full"></span>
                    @endif
                </div>
                <span class="text-sm font-bold text-white/90">Bukti Pembayaran</span>
            </div>
            @if($qrisPaymentCount > 0)
                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-500 text-white shadow-md">{{ $qrisPaymentCount }}</span>
            @endif
        </a>
        
        <button @click="sideOpen=false; chatModal=true" class="w-full flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white relative transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    @if($unreadMsgCount > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-[#1e5128] rounded-full"></span>
                    @endif
                </div>
                <span class="text-sm font-bold text-white/90">Chat Seller</span>
            </div>
            @if($unreadMsgCount > 0)
                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-red-500 text-white shadow-md">{{ $unreadMsgCount }} Baru</span>
            @endif
        </button>

        <a href="{{ route('profile') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Profil & Akun</span>
        </a>

        <a href="{{ route('profile') }}#account-security" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657 1.343-3 3-3h1V7a4 4 0 10-8 0v1h1c1.657 0 3 1.343 3 3v1zm0 0v2m-6 6h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Keamanan Akun</span>
        </a>
    </div>
</div>
