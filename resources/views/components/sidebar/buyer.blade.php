{{-- Buyer Sidebar Nav --}}
@php
    $cartCount        = Auth::user()->carts->count();
    $pendingOrderCount = Auth::user()->orders()->whereIn('status',['pending','accepted'])->count();
    $unreadMsgCount   = \App\Models\Message::whereHas('chat', fn($q) => $q->where('buyer_id', Auth::id()))
                            ->where('sender_id','!=',Auth::id())->where('is_read',false)->count();
@endphp

<a href="{{ route('buyer.dashboard') }}" class="sidebar-item {{ Request::is('buyer/dashboard') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Dashboard</span>
</a>

<div x-show="sideOpen" x-cloak class="px-3 pt-4 pb-1">
    <p class="text-[9px] uppercase font-black tracking-widest" style="color:#4a7a4e">Belanja</p>
</div>

<a href="{{ route('buyer.products') }}" class="sidebar-item {{ Request::is('products*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Produk</span>
</a>

<a href="{{ route('buyer.cart') }}" class="sidebar-item {{ Request::is('cart*') ? 'active' : '' }}">
    <div class="relative flex-shrink-0" style="width:18px;height:18px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
        @if($cartCount > 0)
            <span class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 text-[8px] font-black rounded-full flex items-center justify-center" style="background:#72bf77;color:#fff">{{ $cartCount }}</span>
        @endif
    </div>
    <span x-show="sideOpen" x-cloak class="truncate flex-1">Keranjang</span>
    @if($cartCount > 0)
        <span x-show="sideOpen" x-cloak class="text-[9px] font-black px-1.5 py-0.5 rounded-full ml-auto" style="background:rgba(114,191,119,.25);color:#72bf77">{{ $cartCount }}</span>
    @endif
</a>

<a href="{{ route('buyer.wishlist') }}" class="sidebar-item {{ Request::is('wishlist*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Wishlist</span>
</a>

<div x-show="sideOpen" x-cloak class="px-3 pt-4 pb-1">
    <p class="text-[9px] uppercase font-black tracking-widest" style="color:#4a7a4e">Pesanan</p>
</div>

<a href="{{ route('buyer.orders') }}" class="sidebar-item {{ Request::is('orders*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate flex-1">Pesanan Saya</span>
    @if($pendingOrderCount > 0)
        <span x-show="sideOpen" x-cloak class="text-[9px] font-black px-1.5 py-0.5 rounded-full ml-auto" style="background:rgba(245,158,11,.2);color:#d97706">{{ $pendingOrderCount }}</span>
    @endif
</a>

<button @click="chatModal=true" class="sidebar-item w-full text-left">
    <div class="relative flex-shrink-0" style="width:18px;height:18px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        @if($unreadMsgCount > 0)
            <span class="absolute -top-1.5 -right-1.5 w-3.5 h-3.5 text-[8px] font-black rounded-full flex items-center justify-center" style="background:#dc2626;color:#fff">{{ $unreadMsgCount }}</span>
        @endif
    </div>
    <span x-show="sideOpen" x-cloak class="truncate flex-1">Chat Seller</span>
    @if($unreadMsgCount > 0)
        <span x-show="sideOpen" x-cloak class="text-[9px] font-black px-1.5 py-0.5 rounded-full ml-auto" style="background:rgba(220,38,38,.2);color:#dc2626">{{ $unreadMsgCount }}</span>
    @endif
</button>

<div x-show="sideOpen" x-cloak class="px-3 pt-4 pb-1">
    <p class="text-[9px] uppercase font-black tracking-widest" style="color:#4a7a4e">Akun</p>
</div>

<a href="{{ route('profile') }}" class="sidebar-item {{ Request::is('profile*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Profil Saya</span>
</a>
