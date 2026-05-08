{{-- Seller Sidebar Nav --}}
@php
    $sellerPendingOrders = Auth::user()->store ? Auth::user()->store->orders()->where('status','pending')->count() : 0;
    $sellerUnread = \App\Models\Message::whereHas('chat', fn($q) => $q->where('seller_id', Auth::id()))
                        ->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
    $sellerStatus = Auth::user()->seller_status ?? 'none';
@endphp

{{-- ============================================================
     DESKTOP: full seller menu
     ============================================================ --}}
<div class="hidden lg:block mt-2 pt-1" style="border-top:1px solid rgba(255,255,255,.1)">
    <div x-show="sideOpen" x-cloak class="sb-section-label">
        <span style="color:#a3e6a8">Seller</span>
    </div>

    <a href="{{ route('seller.dashboard') }}" class="sb-item {{ Request::is('seller/dashboard') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Dashboard Seller</span>
    </a>

    <a href="{{ route('seller.products') }}" class="sb-item {{ Request::is('seller/products*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Produk Saya</span>
    </a>

    <a href="{{ route('seller.orders') }}" class="sb-item {{ Request::is('seller/orders*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label flex-1">Pesanan Masuk</span>
        @if($sellerPendingOrders > 0)
            <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-amber">{{ $sellerPendingOrders }}</span>
            <span x-show="!sideOpen" class="sb-dot sb-dot-amber"></span>
        @endif
    </a>

    <a href="{{ route('seller.analytics') }}" class="sb-item {{ Request::is('seller/analytics*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Analitik</span>
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

    <a href="{{ route('seller.settings') }}" class="sb-item {{ Request::is('seller/settings*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <span x-show="sideOpen" x-cloak class="sb-label">Settings Toko</span>
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
     Dirender sebagai secondary menu items
     ============================================================ --}}
<div class="lg:hidden">

    {{-- Role badge --}}
    <div class="flex items-center gap-2 px-1 mb-2.5">
        <span class="sb-role-badge sb-role-seller">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Seller
        </span>
        @if($sellerStatus === 'approved')
            <span class="sb-role-verified">✓ Terverifikasi</span>
        @elseif($sellerStatus === 'pending')
            <span class="sb-role-pending">⏳ Pending</span>
        @endif
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-2 gap-1.5 mb-2.5">
        <a href="{{ route('seller.orders') }}" class="sb-stat-card {{ Request::is('seller/orders*') ? 'sb-stat-active' : '' }}">
            <div class="sb-stat-num">
                {{ $sellerPendingOrders }}
                @if($sellerPendingOrders > 0)<span class="sb-stat-dot sb-stat-dot-amber"></span>@endif
            </div>
            <div class="sb-stat-lbl">Pesanan</div>
        </a>
        <a href="{{ route('seller.messages') }}" class="sb-stat-card {{ Request::is('seller/messages*') ? 'sb-stat-active' : '' }}">
            <div class="sb-stat-num">
                {{ $sellerUnread }}
                @if($sellerUnread > 0)<span class="sb-stat-dot sb-stat-dot-red"></span>@endif
            </div>
            <div class="sb-stat-lbl">Pesan</div>
        </a>
    </div>

    {{-- Divider --}}
    <div class="sb-divider">Kelola Toko</div>

    <a href="{{ route('seller.dashboard') }}" class="sb-item {{ Request::is('seller/dashboard') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </span>
        <span class="sb-label">Dashboard</span>
    </a>

    <a href="{{ route('seller.products') }}" class="sb-item {{ Request::is('seller/products*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
        </span>
        <span class="sb-label">Produk</span>
    </a>

    <a href="{{ route('seller.analytics') }}" class="sb-item {{ Request::is('seller/analytics*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        </span>
        <span class="sb-label">Analitik</span>
    </a>

    <a href="{{ route('seller.settings') }}" class="sb-item {{ Request::is('seller/settings*') ? 'sb-active' : '' }}">
        <span class="sb-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <span class="sb-label">Settings</span>
    </a>

</div>
