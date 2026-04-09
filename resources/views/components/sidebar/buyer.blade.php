<!-- Dashboard General -->
<a href="/buyer/dashboard" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('buyer/dashboard') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Dashboard Pembeli</span>
</a>

<!-- Browse Products -->
<a href="{{ route('buyer.products') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('products*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21H3V5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H17a2 2 0 012 2v14zM9 9h6m-6 4h6m-5 5h4"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Belanja Produk</span>
</a>

<!-- Cart -->
<a href="{{ route('buyer.cart') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('cart*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 6.146A1 1 0 006.7 21h10.6a1 1 0 00.894-.854L19 8m-6 7v6m-4-6v6"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Keranjang</span>
    @php
        $cartCount = Auth::user()->carts->count();
    @endphp
    @if($cartCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-auto bg-accent text-white px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $cartCount }}</span>
    @endif
</a>

<!-- My Orders -->
<a href="{{ route('buyer.orders') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('orders*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Pesanan Saya</span>
    @php
        $pendingOrderCount = Auth::user()->orders()
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
    @endphp
    @if($pendingOrderCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-auto bg-accent text-white px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $pendingOrderCount }}</span>
    @endif
</a>

<!-- Messages/Chat -->
<button @click="openChatsModal = true"
   class="w-full text-left flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold text-gray-500 hover:bg-gray-50 hover:text-gray-900">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Chat Seller</span>
    @php
        $unreadMessagesCount = \App\Models\Message::whereHas('chat', function ($q) {
            $q->where('buyer_id', Auth::id());
        })->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
    @endphp
    @if($unreadMessagesCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-auto bg-accent text-white px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $unreadMessagesCount }}</span>
    @endif
</button>

<!-- Wishlist -->
<a href="{{ route('buyer.wishlist') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('wishlist*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L7 12l.146.146a4.5 4.5 0 006.708 0l2.146-2.146a4.5 4.5 0 00-6.364-6.364L7 7.172V4.318a.75.75 0 00-1.06-.97L2.22 6.248a.75.75 0 000 1.06z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Wishlist</span>
</a>

<!-- Profile -->
<a href="{{ route('profile') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('profile*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Profil Saya</span>
</a>
