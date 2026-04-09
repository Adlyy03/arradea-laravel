@extends('layouts.dashboard')

@section('title', 'Settings - Arradea Seller')
@section('page_title', 'Pengaturan Toko')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-600 to-slate-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">Konfigurasi</p>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Pengaturan <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Toko</span>.</h1>
            <p class="text-gray-200 font-medium text-lg">Kelola informasi dan preferensi toko Anda.</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 p-5 lg:p-10">
        <h2 class="text-2xl font-black text-gray-900 mb-8">Informasi Toko</h2>

        <form class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Nama Toko</label>
                    <input type="text" value="{{ auth()->user()->store->name ?? '' }}" class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Email Kontak</label>
                    <input type="email" value="{{ auth()->user()->email }}" class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-black text-gray-700 mb-2">Deskripsi Toko</label>
                    <textarea rows="4" class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium resize-none">{{ auth()->user()->store->description ?? '' }}</textarea>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100">
                <h3 class="text-xl font-black text-gray-900 mb-6">Preferensi Notifikasi</h3>

                <div class="space-y-4">
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" checked class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                        <span class="font-medium text-gray-700">Email untuk pesanan baru</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" checked class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                        <span class="font-medium text-gray-700">Notifikasi chat dari pembeli</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                        <span class="font-medium text-gray-700">Laporan penjualan mingguan</span>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection