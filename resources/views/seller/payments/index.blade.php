@extends('layouts.dashboard')

@section('title', 'Konfirmasi Pembayaran - Arradea Seller')
@section('page_title', 'Konfirmasi Pembayaran')

@section('content')
@php
    $pendingOrders = $store->orders()
        ->with(['user', 'product'])
        ->where('payment_method', 'qris')
        ->where('payment_status', 'waiting_confirmation')
        ->latest()
        ->get();
    
    $approvedOrders = $store->orders()
        ->with(['user', 'product'])
        ->where('payment_method', 'qris')
        ->where('payment_status', 'paid')
        ->latest()
        ->take(20)
        ->get();
    
    $rejectedOrders = $store->orders()
        ->with(['user', 'product'])
        ->where('payment_method', 'qris')
        ->where('payment_status', 'rejected')
        ->latest()
        ->take(20)
        ->get();
    
    $activeTab = request()->get('tab', 'pending');
@endphp

<div class="space-y-4 lg:space-y-6 fade-up">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl lg:rounded-3xl p-6 lg:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-56 h-56 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white/80 uppercase tracking-wider">Pembayaran Manual</p>
                    <h1 class="text-2xl lg:text-3xl font-black">Konfirmasi QRIS</h1>
                </div>
            </div>
            <p class="text-white/90 text-sm lg:text-base max-w-2xl">Kelola pembayaran QRIS dari buyer: review bukti transfer, setujui atau tolak transaksi.</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 gap-3 lg:gap-4">
        <div class="bg-white rounded-xl lg:rounded-2xl p-4 lg:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <span class="text-sm">⏳</span>
                </div>
                <p class="text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400">Menunggu</p>
            </div>
            <p class="text-2xl lg:text-3xl font-black text-gray-900">{{ $pendingOrders->count() }}</p>
        </div>
        
        <div class="bg-white rounded-xl lg:rounded-2xl p-4 lg:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <span class="text-sm">✅</span>
                </div>
                <p class="text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400">Disetujui</p>
            </div>
            <p class="text-2xl lg:text-3xl font-black text-gray-900">{{ $approvedOrders->count() }}</p>
        </div>
        
        <div class="bg-white rounded-xl lg:rounded-2xl p-4 lg:p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <span class="text-sm">❌</span>
                </div>
                <p class="text-[10px] lg:text-xs font-black uppercase tracking-wider text-gray-400">Ditolak</p>
            </div>
            <p class="text-2xl lg:text-3xl font-black text-gray-900">{{ $rejectedOrders->count() }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-green-900 text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl">❌</span>
            <div class="flex-1">
                <p class="font-black text-red-900 text-sm mb-1">Terjadi Kesalahan</p>
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 bg-gray-50/50">
            <div class="flex overflow-x-auto">
                <a href="?tab=pending" class="flex-1 min-w-[120px] px-4 py-3 lg:py-4 text-center text-sm font-black transition {{ $activeTab === 'pending' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700' }}">
                    ⏳ Menunggu ({{ $pendingOrders->count() }})
                </a>
                <a href="?tab=approved" class="flex-1 min-w-[120px] px-4 py-3 lg:py-4 text-center text-sm font-black transition {{ $activeTab === 'approved' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700' }}">
                    ✅ Disetujui ({{ $approvedOrders->count() }})
                </a>
                <a href="?tab=rejected" class="flex-1 min-w-[120px] px-4 py-3 lg:py-4 text-center text-sm font-black transition {{ $activeTab === 'rejected' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700' }}">
                    ❌ Ditolak ({{ $rejectedOrders->count() }})
                </a>
            </div>
        </div>

        <div class="p-4 lg:p-6">
            @if($activeTab === 'pending')
                @include('seller.payments.partials.pending', ['orders' => $pendingOrders])
            @elseif($activeTab === 'approved')
                @include('seller.payments.partials.approved', ['orders' => $approvedOrders])
            @elseif($activeTab === 'rejected')
                @include('seller.payments.partials.rejected', ['orders' => $rejectedOrders])
            @endif
        </div>
    </div>
</div>
@endsection