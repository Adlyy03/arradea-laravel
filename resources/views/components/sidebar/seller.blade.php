<!-- Dashboard General -->
<a href="/seller/dashboard" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/dashboard') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Dashboard Seller</span>
</a>

<a href="/seller/products" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/products*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V21M4 11v10l8 4"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Manajemen Produk</span>
</a>

<a href="/seller/orders" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/orders*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Order Masuk</span>
    @php
        $pendingOrderCount = Auth::user()->store
            ? Auth::user()->store->orders()->where('status', 'pending')->count()
            : 0;
    @endphp
    @if($pendingOrderCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-auto bg-accent text-white px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $pendingOrderCount }}</span>
    @endif
</a>

<a href="{{ route('seller.analytics') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/analytics*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Analitik</span>
</a>

<a href="{{ route('seller.messages') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/messages*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Pesan</span>
    @php
        $unreadMessagesCount = \App\Models\Message::whereHas('chat', function ($q) {
            $q->where('seller_id', Auth::id());
        })->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
    @endphp
    @if($unreadMessagesCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="ml-auto bg-accent text-white px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $unreadMessagesCount }}</span>
    @endif
</a>

<a href="{{ route('seller.settings') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('seller/settings*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Pengaturan</span>
</a>

<!-- Profile -->
<a href="{{ route('profile') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('profile*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Profil Saya</span>
</a>
