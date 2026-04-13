@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Arradea')
@section('page_title', 'Performa Marketplace Arradea')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header Banner Banner Banner -->
    <div class="bg-primary-900 p-8 lg:p-16 lg:p-12 lg:p-24 rounded-[5rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-accent rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8 lg:gap-16">
            <div class="space-y-6 text-center lg:text-left">
                <p class="text-xs font-black uppercase tracking-widest text-primary-200">Executive Summary</p>
                <h1 class="text-4xl lg:text-6xl lg:text-8xl font-black tracking-tighter leading-[0.8] mb-6">Total <span class="text-accent underline underline-offset-8 decoration-8">Profit</span>.</h1>
                <h3 class="text-5xl lg:text-7xl font-black text-white/95">Rp 1.250.000.000</h3>
                <div class="flex flex-wrap justify-center lg:justify-start gap-4 pt-8">
                    <button class="px-5 lg:px-10 py-5 bg-white text-primary-900 rounded-2xl lg:rounded-3xl font-black text-lg hover:scale-110 active:scale-95 transition-all">Download Report</button>
                    <button class="px-5 lg:px-10 py-5 bg-white/10 backdrop-blur-xl border border-white/20 text-white rounded-2xl lg:rounded-3xl font-black text-lg hover:bg-white/20 transition-all">Lacak Performa</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full md:w-max">
                <div class="px-5 lg:px-10 py-6 lg:py-12 bg-white/10 backdrop-blur-xl border border-white/10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] text-center space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-primary-200 mb-1">Transaksi Sukses</p>
                    <p class="text-4xl lg:text-6xl font-black leading-none">12.5k</p>
                    <p class="text-xs font-bold text-green-300">+25% Trend</p>
                </div>
                <div class="px-5 lg:px-10 py-6 lg:py-12 bg-white/10 backdrop-blur-xl border border-white/10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] text-center space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-primary-200 mb-1">Cari User Baru</p>
                    <p class="text-4xl lg:text-6xl font-black leading-none">2.4k</p>
                    <p class="text-xs font-bold text-primary-300">Target Tercapai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Triple Stats Triple Stats Triple Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-10">
        <div class="bg-white p-6 lg:p-12 rounded-[3.5rem] shadow-sm border border-gray-100 flex items-center gap-8 group hover:shadow-2xl transition duration-500">
            <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-[2.5rem] flex items-center justify-center text-4xl group-hover:scale-110 transition duration-500">🏬</div>
            <div class="space-y-1">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Seller</p>
                <h4 class="text-3xl lg:text-5xl font-black text-gray-900 leading-none tracking-tighter">{{ \App\Models\User::where('is_seller', true)->count() }} Toko</h4>
                <a href="/admin/sellers" class="block pt-2 text-sm font-bold text-primary-600 uppercase tracking-widest">Detail Seller →</a>
            </div>
        </div>
        <div class="bg-white p-6 lg:p-12 rounded-[3.5rem] shadow-sm border border-gray-100 flex items-center gap-8 group hover:shadow-2xl transition duration-500">
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-[2.5rem] flex items-center justify-center text-4xl group-hover:scale-110 transition duration-500">🗳️</div>
            <div class="space-y-1">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Produk Aktif</p>
                <h4 class="text-3xl lg:text-5xl font-black text-gray-900 leading-none tracking-tighter">{{ \App\Models\Product::count() }} Item</h4>
                <p class="block pt-2 text-sm font-bold text-gray-400 uppercase tracking-widest">Menunggu Kurasi</p>
            </div>
        </div>
        <div class="bg-white p-6 lg:p-12 rounded-[3.5rem] shadow-sm border border-gray-100 flex items-center gap-8 group hover:shadow-2xl transition duration-500">
            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-[2.5rem] flex items-center justify-center text-4xl group-hover:scale-110 transition duration-500">👤</div>
            <div class="space-y-1">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Data Buyer</p>
                <h4 class="text-3xl lg:text-5xl font-black text-gray-900 leading-none tracking-tighter">{{ \App\Models\User::where('is_seller', false)->where('role', '!=', 'admin')->count() }} User</h4>
                <p class="block pt-2 text-sm font-bold text-green-500 uppercase tracking-widest">Growth Stabil</p>
            </div>
        </div>
    </div>
</div>
@endsection
