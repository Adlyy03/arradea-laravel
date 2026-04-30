{{-- Admin Sidebar Nav Items --}}
@php
    $pendingCount = \App\Models\User::whereNotNull('phone_verified_at')
        ->whereNull('access_code_id')->where('role','!=','admin')->count()
        + \App\Models\User::where('seller_otp_verified',true)->where('is_seller',false)->count();
@endphp

<a href="/admin/dashboard" class="sidebar-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Dashboard</span>
</a>

<div x-show="sideOpen" x-cloak class="px-3 pt-4 pb-1">
    <p class="text-[9px] uppercase font-black tracking-widest" style="color:#4a7a4e">Manajemen</p>
</div>

<a href="/admin/sellers" class="sidebar-item {{ Request::is('admin/sellers*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Data Seller</span>
</a>

<a href="{{ route('admin.users.index') }}" class="sidebar-item {{ Request::is('admin/users') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Semua Pengguna</span>
</a>

<a href="{{ route('admin.verifications.index') }}" class="sidebar-item flex justify-between {{ Request::is('admin/verifications*') ? 'active' : '' }}">
    <div class="flex items-center gap-3">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span x-show="sideOpen" x-cloak class="truncate">Verifikasi</span>
    </div>
    @if($pendingCount > 0)
        <span x-show="sideOpen" x-cloak class="text-[9px] font-black px-1.5 py-0.5 rounded-full" style="background:#dc2626;color:#fff">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('admin.users.verification') }}" class="sidebar-item {{ Request::is('admin/users-verification*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Verif. Pendaftar</span>
</a>

<div x-show="sideOpen" x-cloak class="px-3 pt-4 pb-1">
    <p class="text-[9px] uppercase font-black tracking-widest" style="color:#4a7a4e">Analytics</p>
</div>

<a href="{{ route('admin.map-users') }}" class="sidebar-item {{ Request::is('admin/map-users*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Live Map</span>
</a>

<a href="{{ route('admin.access-codes.index') }}" class="sidebar-item {{ Request::is('admin/access-codes*') ? 'active' : '' }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 016 0v2H9z"/></svg>
    <span x-show="sideOpen" x-cloak class="truncate">Kode Akses</span>
</a>
