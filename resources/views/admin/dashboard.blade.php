@extends('layouts.dashboard')
@section('title', 'Admin Dashboard — Arradea')
@section('page_title', 'Admin Dashboard')

@section('content')
@php
    $totalSellers = \App\Models\User::where('is_seller',true)->count();
    $totalProducts = \App\Models\Product::count();
    $totalBuyers = \App\Models\User::where('is_seller',false)->where('role','!=','admin')->count();
    $totalUsers = \App\Models\User::where('role','!=','admin')->count();
    $pendingVerif = \App\Models\User::whereNotNull('phone_verified_at')->whereNull('access_code_id')->where('role','!=','admin')->count();
    $pendingSellerOtp = \App\Models\User::where('seller_otp_verified',true)->where('is_seller',false)->count();
    $pendingTotal = $pendingVerif + $pendingSellerOtp;
    $recentUsers = \App\Models\User::where('role','!=','admin')->latest()->take(8)->get();
@endphp

<div class="space-y-5 fade-up">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8 text-white" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full opacity-10" style="background:#72bf77;filter:blur(40px)"></div>
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Executive Dashboard</p>
                <h1 class="text-3xl lg:text-4xl font-black tracking-tight leading-tight">Arradea<br><span style="color:#72bf77">Marketplace</span> Admin</h1>
                <p class="text-white/50 text-sm mt-2">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="px-5 py-4 rounded-2xl text-center" style="background:rgba(114,191,119,.12);border:1px solid rgba(114,191,119,.2)">
                    <p class="text-[9px] uppercase tracking-widest font-black mb-1" style="color:#72bf77">Total User</p>
                    <p class="text-3xl font-black">{{ $totalUsers }}</p>
                </div>
                <div class="px-5 py-4 rounded-2xl text-center" style="background:rgba(114,191,119,.12);border:1px solid rgba(114,191,119,.2)">
                    <p class="text-[9px] uppercase tracking-widest font-black mb-1" style="color:#72bf77">Produk Aktif</p>
                    <p class="text-3xl font-black">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0" style="background:rgba(59,130,246,.1)">🏬</div>
            <div>
                <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-0.5">Total Seller</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalSellers }}</p>
                <a href="/admin/sellers" class="text-[11px] font-bold" style="color:#72bf77">Lihat detail →</a>
            </div>
        </div>
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0" style="background:rgba(245,158,11,.1)">📦</div>
            <div>
                <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-0.5">Produk Aktif</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalProducts }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0" style="background:rgba(34,197,94,.1)">👤</div>
            <div>
                <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-0.5">Total Buyer</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalBuyers }}</p>
                <p class="text-[11px] font-bold text-green-500">Growth stabil</p>
            </div>
        </div>
    </div>

    {{-- Pending Actions --}}
    @if($pendingTotal > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-black text-amber-800">{{ $pendingTotal }} Aksi Perlu Perhatian</h3>
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
            @if($pendingVerif > 0)
            <div class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-amber-100">
                <div>
                    <p class="text-sm font-bold text-gray-900">Verifikasi User Baru</p>
                    <p class="text-xs text-gray-500">{{ $pendingVerif }} pendaftar menunggu</p>
                </div>
                <a href="{{ route('admin.verifications.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white" style="background:#72bf77">Review</a>
            </div>
            @endif
            @if($pendingSellerOtp > 0)
            <div class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-amber-100">
                <div>
                    <p class="text-sm font-bold text-gray-900">Upgrade Seller</p>
                    <p class="text-xs text-gray-500">{{ $pendingSellerOtp }} pengajuan OTP</p>
                </div>
                <a href="{{ route('admin.verifications.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white" style="background:#72bf77">Review</a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Recent Users --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pengguna Terbaru</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold" style="color:#72bf77">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/60">
                        <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Pengguna</th>
                        <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Tipe</th>
                        <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentUsers as $u)
                    @php $isVerif = $u->accessCode && $u->accessCode->is_active; @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0" style="background:rgba(114,191,119,.12);color:#3fa348">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $u->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $u->is_seller ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $u->is_seller ? 'Seller' : 'Buyer' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $isVerif ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $isVerif ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-500">{{ $u->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
