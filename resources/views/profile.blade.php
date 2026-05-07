@extends('layouts.dashboard')
@section('title', 'Profil Saya — Arradea')
@section('page_title', 'Profil')

@push('styles')
<style>
    .profile-card { background:rgba(255,255,255,0.75); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(114,191,119,0.12); transition:all .3s cubic-bezier(0.4,0,0.2,1); }
    .profile-card:hover { transform:translateY(-2px); box-shadow:0 12px 32px rgba(114,191,119,.12); }
    .action-card { transition:all .3s cubic-bezier(0.4,0,0.2,1); }
    .action-card:hover { transform:translateY(-3px) scale(1.02); box-shadow:0 12px 28px rgba(114,191,119,.15); }
    
    /* Mobile profile optimizations - Ultra Compact */
    @media(max-width:1023px){
        .profile-header h1 { font-size: 20px !important; }
        .profile-header p { font-size: 11px !important; }
        .profile-card { border-radius: 16px !important; }
        .profile-avatar-section { padding: 16px !important; }
        .profile-avatar { width: 56px !important; height: 56px !important; font-size: 24px !important; border-radius: 16px !important; }
        .profile-avatar-name { font-size: 18px !important; }
        .profile-avatar-phone { font-size: 12px !important; margin-top: 2px !important; }
        .profile-info-section { padding: 16px !important; }
        .profile-info-card { padding: 12px !important; border-radius: 12px !important; }
        .profile-info-label { font-size: 10px !important; margin-bottom: 6px !important; }
        .profile-info-value { font-size: 13px !important; }
        .profile-mode-badge { padding: 4px 8px !important; font-size: 10px !important; border-radius: 8px !important; }
        .profile-cta { padding: 16px !important; border-radius: 16px !important; }
        .profile-cta h3 { font-size: 16px !important; line-height: 1.3 !important; }
        .profile-cta p { font-size: 12px !important; margin-top: 4px !important; }
        .profile-cta-label { font-size: 9px !important; margin-bottom: 8px !important; }
        .profile-cta-btn { padding: 10px 16px !important; font-size: 12px !important; border-radius: 12px !important; }
        .profile-quick-actions { padding: 16px !important; border-radius: 16px !important; }
        .profile-quick-actions h3 { font-size: 11px !important; margin-bottom: 12px !important; }
        .profile-quick-actions .action-card { padding: 12px !important; border-radius: 12px !important; gap: 8px !important; }
        .profile-quick-actions .action-card span:first-child { font-size: 24px !important; }
        .profile-quick-actions .action-card span:last-child { font-size: 11px !important; }
        .profile-alert { padding: 12px !important; border-radius: 12px !important; }
        .profile-alert-icon { font-size: 24px !important; }
        .profile-alert-title { font-size: 13px !important; }
        .profile-alert-desc { font-size: 11px !important; margin-top: 2px !important; }
        .profile-alert-btn { padding: 8px 12px !important; font-size: 11px !important; border-radius: 10px !important; }
        .profile-success { padding: 12px !important; border-radius: 12px !important; font-size: 12px !important; }
        .profile-success svg { width: 16px !important; height: 16px !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-up">

    {{-- Header --}}
    <div class="profile-header flex items-center justify-between">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-gray-900">Profil <span class="bg-gradient-to-r from-[#72bf77] to-[#4db85a] bg-clip-text text-transparent">Saya</span></h1>
            <p class="text-xs lg:text-sm text-gray-500 mt-1 font-medium">Kelola informasi akun Anda</p>
        </div>
    </div>

    @if(session('success'))
    <div class="profile-success flex items-center gap-2 lg:gap-3 p-4 lg:p-5 rounded-xl lg:rounded-2xl text-green-700 text-xs lg:text-sm font-semibold shadow-lg" style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2)">
        <div class="w-6 lg:w-7 h-6 lg:h-7 rounded-lg lg:rounded-xl bg-green-500 flex items-center justify-center text-white flex-shrink-0">
            <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        {{ session('success') }}
    </div>
    @endif

    {{-- Profile Card --}}
    <div class="profile-card rounded-2xl lg:rounded-3xl overflow-hidden shadow-lg">
        <div class="profile-avatar-section p-6 lg:p-8 border-b border-gray-100/60" style="background:linear-gradient(135deg,rgba(240,250,241,0.6),rgba(255,255,255,0.4))">
            <div class="flex items-center gap-3 lg:gap-5">
                <div class="profile-avatar w-16 lg:w-20 h-16 lg:h-20 rounded-2xl lg:rounded-3xl flex items-center justify-center text-2xl lg:text-3xl font-black flex-shrink-0 shadow-lg" style="background:linear-gradient(135deg,rgba(114,191,119,0.2),rgba(114,191,119,0.1));border:2px solid rgba(114,191,119,0.2)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="profile-avatar-name text-xl lg:text-2xl font-black text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="profile-avatar-phone text-xs lg:text-sm text-gray-500 mt-0.5 lg:mt-1 font-medium">{{ auth()->user()->phone }}</p>
                </div>
            </div>
        </div>

        <div class="profile-info-section p-6 lg:p-8 space-y-4 lg:space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-5">
                <div class="profile-info-card p-4 lg:p-5 rounded-xl lg:rounded-2xl border transition-all duration-300 hover:shadow-md" style="background:rgba(249,250,251,0.6);border-color:rgba(229,231,235,0.8)">
                    <p class="profile-info-label text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400 mb-1.5 lg:mb-2">Nomor Telepon</p>
                    <p class="profile-info-value text-sm lg:text-base font-bold text-gray-900">{{ auth()->user()->phone ?? '—' }}</p>
                </div>

                <div class="profile-info-card p-4 lg:p-5 rounded-xl lg:rounded-2xl border transition-all duration-300 hover:shadow-md" style="background:rgba(249,250,251,0.6);border-color:rgba(229,231,235,0.8)">
                    <p class="profile-info-label text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400 mb-1.5 lg:mb-2">Mode Akun</p>
                    <span class="profile-mode-badge inline-flex items-center gap-1.5 lg:gap-2 px-2.5 lg:px-3 py-1 lg:py-1.5 rounded-lg lg:rounded-xl text-[10px] lg:text-xs font-black uppercase {{ auth()->user()->is_seller ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ auth()->user()->is_seller ? '🏪 Seller + Buyer' : '🛒 Buyer' }}
                    </span>
                </div>

                @if(auth()->user()->store)
                <div class="profile-info-card p-4 lg:p-5 rounded-xl lg:rounded-2xl border sm:col-span-2 transition-all duration-300 hover:shadow-md" style="background:rgba(240,250,241,0.4);border-color:rgba(114,191,119,0.2)">
                    <p class="profile-info-label text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400 mb-1.5 lg:mb-2">Nama Toko</p>
                    <p class="profile-info-value text-sm lg:text-base font-bold text-gray-900">{{ auth()->user()->store->name }}</p>
                    <p class="text-xs lg:text-sm text-gray-500 mt-1">Status: <span class="font-bold" style="color:#72bf77">{{ auth()->user()->store->status ?? 'pending' }}</span></p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Seller CTA --}}
    @if(!auth()->user()->is_seller)
    <div class="profile-cta relative overflow-hidden rounded-2xl lg:rounded-3xl p-6 lg:p-8 shadow-xl" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-12" style="background:#72bf77;filter:blur(70px)"></div>
        <div class="absolute -bottom-16 -left-12 w-48 h-48 rounded-full opacity-10" style="background:#4db85a;filter:blur(50px)"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 lg:gap-6">
            <div class="text-white">
                <p class="profile-cta-label text-[9px] lg:text-xs font-black uppercase tracking-wider mb-2 lg:mb-3" style="color:#72bf77">Upgrade Akun</p>
                <h3 class="text-lg lg:text-2xl font-black tracking-tight leading-tight">Jadi Seller & Mulai Berjualan</h3>
                <p class="text-white/70 text-xs lg:text-sm mt-1.5 lg:mt-2 leading-relaxed">Buka toko online dan jangkau pembeli.</p>
            </div>
            <a href="{{ route('seller.apply') }}" class="profile-cta-btn flex-shrink-0 px-5 lg:px-7 py-2.5 lg:py-3.5 rounded-xl lg:rounded-2xl text-xs lg:text-sm font-bold text-gray-900 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl active:scale-95" style="background:linear-gradient(135deg,#72bf77,#4db85a);box-shadow:0 8px 24px rgba(114,191,119,.3)">
                🚀 Daftar Sekarang
            </a>
        </div>
    </div>
    @elseif(auth()->user()->seller_otp_verified && !auth()->user()->is_seller)
    <div class="profile-alert flex items-center justify-between p-5 lg:p-6 rounded-xl lg:rounded-2xl shadow-lg" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2)">
        <div class="flex items-center gap-3 lg:gap-4">
            <span class="profile-alert-icon text-2xl lg:text-3xl">⏳</span>
            <div>
                <p class="profile-alert-title text-sm lg:text-base font-black text-amber-800">Pengajuan Ditinjau</p>
                <p class="profile-alert-desc text-xs lg:text-sm text-amber-600 mt-0.5">Admin sedang memverifikasi data.</p>
            </div>
        </div>
        <a href="{{ route('seller.pending') }}" class="profile-alert-btn px-4 lg:px-5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-amber-700 hover:text-white transition-all duration-300 flex-shrink-0" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3)">Cek Status</a>
    </div>
    @elseif(auth()->user()->seller_status === 'pending' && !auth()->user()->seller_otp_verified)
    <div class="profile-alert flex items-center justify-between p-5 lg:p-6 rounded-xl lg:rounded-2xl shadow-lg" style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.2)">
        <div class="flex items-center gap-3 lg:gap-4">
            <span class="profile-alert-icon text-2xl lg:text-3xl">📱</span>
            <div>
                <p class="profile-alert-title text-sm lg:text-base font-black text-yellow-800">Verifikasi OTP Belum Selesai</p>
                <p class="profile-alert-desc text-xs lg:text-sm text-yellow-600 mt-0.5">Selesaikan verifikasi OTP.</p>
            </div>
        </div>
        <a href="{{ route('seller.verify-otp') }}" class="profile-alert-btn px-4 lg:px-5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl text-xs lg:text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg flex-shrink-0" style="background:linear-gradient(135deg,#72bf77,#4db85a)">Verifikasi</a>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="profile-quick-actions profile-card rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-lg">
        <h3 class="text-xs lg:text-sm font-black text-gray-700 uppercase tracking-wider mb-4 lg:mb-5">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4">
            @if(auth()->user()->is_seller)
            <a href="{{ route('seller.dashboard') }}" class="action-card flex flex-col items-center gap-2 lg:gap-3 p-4 lg:p-5 rounded-xl lg:rounded-2xl text-center shadow-sm" style="background:rgba(240,250,241,0.6);border:1px solid rgba(114,191,119,0.15)">
                <span class="text-2xl lg:text-3xl">🏪</span>
                <span class="text-xs lg:text-sm font-bold text-gray-700">Dashboard Seller</span>
            </a>
            @endif
            <a href="{{ route('buyer.dashboard') }}" class="action-card flex flex-col items-center gap-2 lg:gap-3 p-4 lg:p-5 rounded-xl lg:rounded-2xl text-center shadow-sm" style="background:rgba(239,246,255,0.6);border:1px solid rgba(191,219,254,0.4)">
                <span class="text-2xl lg:text-3xl">🛍️</span>
                <span class="text-xs lg:text-sm font-bold text-gray-700">Dashboard Buyer</span>
            </a>
            <a href="{{ route('buyer.orders') }}" class="action-card flex flex-col items-center gap-2 lg:gap-3 p-4 lg:p-5 rounded-xl lg:rounded-2xl text-center shadow-sm" style="background:rgba(254,243,199,0.6);border:1px solid rgba(253,230,138,0.4)">
                <span class="text-2xl lg:text-3xl">📋</span>
                <span class="text-xs lg:text-sm font-bold text-gray-700">Pesanan Saya</span>
            </a>
            <a href="{{ route('buyer.products') }}" class="action-card flex flex-col items-center gap-2 lg:gap-3 p-4 lg:p-5 rounded-xl lg:rounded-2xl text-center shadow-sm" style="background:rgba(240,253,244,0.6);border:1px solid rgba(187,247,208,0.4)">
                <span class="text-2xl lg:text-3xl">🛒</span>
                <span class="text-xs lg:text-sm font-bold text-gray-700">Belanja</span>
            </a>
        </div>
    </div>
</div>
@endsection
