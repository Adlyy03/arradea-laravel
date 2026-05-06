{{-- Buyer Sidebar Nav --}}
@php
    $cartCount        = Auth::user()->carts->count();
    $pendingOrderCount = Auth::user()->orders()->whereIn('status',['pending','accepted'])->count();
    $unreadMsgCount   = \App\Models\Message::whereHas('chat', fn($q) => $q->where('buyer_id', Auth::id()))
                            ->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
@endphp

<a href="{{ route('buyer.dashboard') }}" class="sb-item {{ Request::is('buyer/dashboard') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Dashboard</span>
</a>

<div x-show="sideOpen" x-cloak class="sb-section-label">
    <span>Belanja</span>
</div>

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

{{-- Chat moved to bottom nav on mobile, keep in sidebar for desktop --}}
<div class="hidden lg:block">
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
</div>

<div x-show="sideOpen" x-cloak class="sb-section-label">
    <span>Akun</span>
</div>

<a href="{{ route('profile') }}" class="sb-item {{ Request::is('profile*') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Profil</span>
</a>

{{-- Wishlist moved to less prominent position --}}
<a href="{{ route('buyer.wishlist') }}" class="sb-item {{ Request::is('wishlist*') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Wishlist</span>
</a>
