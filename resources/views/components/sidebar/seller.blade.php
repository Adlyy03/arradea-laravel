{{-- Seller Sidebar Nav --}}
@php
    $sellerPendingOrders = Auth::user()->store ? Auth::user()->store->orders()->whereIn('status', ['pending', 'processing'])->count() : 0;
    $sellerWaitingPayments = Auth::user()->store ? Auth::user()->store->orders()->where('payment_method', 'qris')->where('payment_status', 'waiting_confirmation')->count() : 0;
    $sellerUnread = \App\Models\Message::whereHas('chat', fn($q) => $q->where('seller_id', Auth::id()))
                        ->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
    $sellerStatus = Auth::user()->seller_status ?? 'none';
    $productCount = Auth::user()->store ? Auth::user()->store->products()->count() : 0;
@endphp

{{-- ============================================================
     DESKTOP: full seller menu
     ============================================================ --}}
<div class="hidden lg:block">
    <a href="{{ route('seller.dashboard') }}" class="sb-item {{ Request::is('seller/dashboard') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Dashboard</span>
    </a>

    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Toko</span></div>

    <a href="{{ route('seller.products') }}" class="sb-item {{ Request::is('seller/products*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Produk</span>
        <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-green">{{ $productCount }}</span>
    </a>

    <a href="{{ route('seller.orders') }}" class="sb-item {{ Request::is('seller/orders*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Pesanan</span>
        @if($sellerPendingOrders > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-amber">{{ $sellerPendingOrders }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-amber"></span>
        @endif
    </a>

    <a href="{{ route('seller.payments') }}" class="sb-item {{ Request::is('seller/payments*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7h18M3 12h18M3 17h18M6 7v10m12-10v10"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Konfirmasi Pembayaran</span>
        @if($sellerWaitingPayments > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-green">{{ $sellerWaitingPayments }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-green"></span>
        @endif
    </a>

    <a href="{{ route('seller.messages') }}" class="sb-item {{ Request::is('seller/messages*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Pesan</span>
        @if($sellerUnread > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-red">{{ $sellerUnread }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-red"></span>
        @endif
    </a>

    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Analitik</span></div>

    <a href="{{ route('seller.analytics') }}" class="sb-item {{ Request::is('seller/analytics*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Laporan</span>
    </a>

<<<<<<< HEAD
    

    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Pengaturan</span></div>
=======
    <div x-show="sideOpen" x-cloak class="sb-section-label"><span>Akun</span></div>

    <a href="{{ route('profile') }}" class="sb-item {{ Request::is('profile*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Profil & Akun</span>
    </a>
>>>>>>> 1688c02551a4c3a5c36573e09b0fed8b8d385f24

    <a href="{{ route('profile') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Profil & Akun</span>
        </a>


    <a href="{{ route('seller.settings') }}" class="sb-item {{ Request::is('seller/settings*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Pengaturan</span>
    </a>

    {{-- Seller status chip --}}
    @if($sellerStatus === 'pending')
        <div x-show="sideOpen" x-cloak class="sb-status-chip sb-chip-amber">
            <span class="sb-chip-dot"></span>
            <div>
                <p class="sb-chip-title">Pending</p>
                <p class="sb-chip-desc">Menunggu persetujuan</p>
            </div>
        </div>
    @elseif($sellerStatus === 'approved')
        <div x-show="sideOpen" x-cloak class="sb-status-chip sb-chip-green">
            <span class="sb-chip-dot"></span>
            <div>
                <p class="sb-chip-title">✓ Verified Seller</p>
            </div>
        </div>
    @endif
</div>

{{-- ============================================================
     MOBILE: Seller section - compact mode
     ============================================================ --}}
<div class="lg:hidden mt-2 space-y-4 px-2">
    {{-- Seller Info Card --}}
    <div class="p-3 bg-white/10 rounded-xl border border-white/20 backdrop-blur-md shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    Mode Seller
                </span>
                @if($sellerStatus === 'approved')
                    <span class="text-[10px] font-bold text-green-400 flex items-center gap-1 bg-green-500/10 px-2 py-0.5 rounded-md border border-green-500/20">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Aktif
                    </span>
                @elseif($sellerStatus === 'pending')
                    <span class="text-[10px] font-bold text-amber-400 flex items-center gap-1 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                        ⏳ Pending
                    </span>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('seller.products') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all">
                <span class="text-xl font-black text-white mb-1">{{ $productCount }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Produk</span>
            </a>
            <a href="{{ route('seller.orders') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all relative">
                @if($sellerPendingOrders > 0)
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-amber-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(251,191,36,0.8)]"></span>
                @endif
                <span class="text-xl font-black text-white mb-1">{{ $sellerPendingOrders }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Pesanan</span>
            </a>
            <a href="{{ route('seller.payments') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all relative">
                @if($sellerWaitingPayments > 0)
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></span>
                @endif
                <span class="text-xl font-black text-white mb-1">{{ $sellerWaitingPayments }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Pembayaran</span>
            </a>
            <a href="{{ route('seller.messages') }}" @click="if(isMobile) sideOpen=false" class="flex flex-col items-center justify-center p-3 rounded-lg bg-black/20 hover:bg-black/30 border border-white/5 transition-all relative">
                @if($sellerUnread > 0)
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                @endif
                <span class="text-xl font-black text-white mb-1">{{ $sellerUnread }}</span>
                <span class="text-[9px] uppercase tracking-wider text-white/60 font-bold">Pesan</span>
            </a>
        </div>
    </div>

    {{-- Menu Actions --}}
    <div class="space-y-1.5">
        <a href="{{ route('seller.dashboard') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Dashboard Toko</span>
        </a>

        <a href="{{ route('seller.analytics') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Analitik Penjualan</span>
        </a>

        <a href="{{ route('seller.settings') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Pengaturan Toko</span>
        </a>

        <a href="{{ route('profile') }}" @click="if(isMobile) sideOpen=false" class="w-full flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-white/10 transition-all group {{ Request::is('profile*') ? 'ring-1 ring-white/20' : '' }}">
            <div class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-sm font-bold text-white/90">Profil & Akun</span>
        </a>
    </div>
</div>
