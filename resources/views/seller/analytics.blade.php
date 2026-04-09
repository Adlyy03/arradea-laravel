@extends('layouts.dashboard')

@section('title', 'Analytics - Arradea Seller')
@section('page_title', 'Analitik Toko')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">Performa Bisnis</p>
                <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Analitik <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Toko</span>.</h1>
                <p class="text-blue-100 font-medium text-lg">Pantau performa penjualan dan pertumbuhan bisnis Anda.</p>
            </div>
            <a href="{{ route('seller.analytics.export') }}" class="px-6 py-4 bg-white text-blue-600 rounded-2xl font-black shadow-lg hover:bg-gray-50 hover:-translate-y-1 transition-all flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV Laporan
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl">📦</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Produk</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['total_products'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-2xl">💰</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Pendapatan</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">Rp {{ number_format($analytics['total_revenue'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-2xl">🛒</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Pesanan</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['total_orders'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-2xl">📈</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Pesanan Bulan Ini</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['monthly_orders'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-8">Status Pesanan</h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-amber-400 rounded-full"></div>
                        <span class="font-bold text-gray-700">Menunggu</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['pending_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-green-400 rounded-full"></div>
                        <span class="font-bold text-gray-700">Selesai</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['completed_orders'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-8">Tips Optimasi</h3>
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <p class="text-sm font-bold text-blue-800">Tambah Produk Baru</p>
                    <p class="text-xs text-blue-600 mt-1">Produk yang beragam menarik lebih banyak pembeli.</p>
                </div>
                <div class="p-4 bg-green-50 rounded-2xl border border-green-100">
                    <p class="text-sm font-bold text-green-800">Responsif terhadap Pesanan</p>
                    <p class="text-xs text-green-600 mt-1">Proses pesanan dengan cepat untuk ulasan positif.</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-2xl border border-purple-100">
                    <p class="text-sm font-bold text-purple-800">Promosikan Produk</p>
                    <p class="text-xs text-purple-600 mt-1">Gunakan diskon untuk meningkatkan penjualan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection