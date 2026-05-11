<?php
    $pendingBuyerCount = \App\Models\User::whereNotNull('phone_verified_at')
        ->whereNull('access_code_id')->where('role','!=','admin')
        ->where(fn($q) => $q->whereNull('seller_status')->orWhere('seller_status','none'))->count();
    $pendingSellerCount = \App\Models\User::whereNotNull('phone_verified_at')
        ->whereNotNull('access_code_id')->where('seller_status','pending')->where('is_seller',false)->count();
    $pendingCount = $pendingBuyerCount + $pendingSellerCount;
    $pendingComplaints = \App\Models\Complaint::count();
?>

<a href="/admin/dashboard" class="sb-item {{ Request::is('admin/dashboard') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Dashboard</span>
</a>

<div x-show="sideOpen" x-cloak class="sb-section-label">
    <span>Manajemen</span>
</div>

<a href="{{ route('admin.users.index') }}" class="sb-item {{ Request::is('admin/users') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Data Pengguna</span>
</a>

<a href="{{ route('admin.categories.index') }}" class="sb-item {{ Request::is('admin/categories*') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Kategori Produk</span>
</a>

<a href="{{ route('admin.products.index') }}" class="sb-item {{ Request::is('admin/products*') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Kelola Produk</span>
</a>

<a href="{{ route('admin.users.verification') }}" class="sb-item {{ Request::is('admin/users-verification*') ? 'sb-active' : '' }}">
    <span class="sb-icon" style="position:relative">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        @if($pendingCount > 0)
            <span class="sb-icon-dot sb-icon-dot-red"></span>
        @endif
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label flex-1">Verif. Pendaftar</span>
    @if($pendingCount > 0)
        <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-red">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('admin.complaints.index') }}" class="sb-item {{ Request::is('admin/complaints*') ? 'sb-active' : '' }}">
    <span class="sb-icon" style="position:relative">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @if($pendingComplaints > 0)
            <span class="sb-icon-dot sb-icon-dot-red"></span>
        @endif
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label flex-1">Keluhan User</span>
    @if($pendingComplaints > 0)
        <span x-show="sideOpen" x-cloak class="sb-badge sb-badge-red">{{ $pendingComplaints }}</span>
    @endif
</a>

<div x-show="sideOpen" x-cloak class="sb-section-label">
    <span>Analytics</span>
</div>

<a href="{{ route('admin.map-users') }}" class="sb-item {{ Request::is('admin/map-users*') ? 'sb-active' : '' }}">
    <span class="sb-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
    </span>
    <span x-show="sideOpen" x-cloak class="sb-label">Live Map</span>
</a>


