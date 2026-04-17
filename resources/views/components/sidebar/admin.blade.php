<!-- Dashboard General -->
<a href="/admin/dashboard" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('admin/dashboard') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Admin Panel</span>
</a>

<a href="/admin/sellers" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('admin/sellers*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Data Pelapak (Seller)</span>
</a>

<!-- New: Verifikasi Pengguna Baru -->
@php
    $pendingVerificationsCount = \App\Models\User::whereNotNull('phone_verified_at')
        ->whereNull('access_code_id')
        ->where('role', '!=', 'admin')
        ->count()
        + \App\Models\User::where('seller_otp_verified', true)->where('is_seller', false)->count();
@endphp
<a href="{{ route('admin.verifications.index') }}" 
   class="flex items-center justify-between px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('admin/verifications*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <div class="flex items-center gap-4">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Verifikasi Pendaftar</span>
    </div>
    @if($pendingVerificationsCount > 0)
        <span x-show="sidebarOpen || window.innerWidth < 1024" class="inline-flex items-center justify-center w-6 h-6 text-xs font-black text-white bg-red-500 rounded-full shrink-0 animate-bounce">
            {{ $pendingVerificationsCount }}
        </span>
    @endif
</a>

<a href="{{ route('admin.users.index') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('admin/users*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Semua Pengguna</span>
</a>

<a href="{{ route('admin.access-codes.index') }}" 
   class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all font-bold {{ Request::is('admin/access-codes*') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
    <span x-show="sidebarOpen || window.innerWidth < 1024" class="truncate">Kode Akses</span>
</a>
