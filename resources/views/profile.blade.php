@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 lg:py-16 px-4">
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-5 lg:p-10 grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">Profil Saya</h1>
                    <p class="mt-3 text-gray-500">Kelola akun Anda, lihat peran saat ini, dan ajukan menjadi seller dari halaman ini.</p>
                </div>

                @if(session('success'))
                    <div class="rounded-2xl lg:rounded-3xl border border-green-100 bg-green-50 p-6 text-green-700 font-bold">{{ session('success') }}</div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-6 bg-gray-50">
                        <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Nama</h2>
                        <p class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</p>
                    </div>

                    <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-6 bg-gray-50">
                        <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Email</h2>
                        <p class="text-lg font-bold text-gray-900">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-6 bg-gray-50">
                        <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Mode Akun</h2>
                        <p class="text-lg font-bold text-primary-700 uppercase">{{ auth()->user()->is_seller ? 'Seller + Buyer' : 'Buyer' }}</p>
                    </div>

                    <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-6 bg-gray-50">
                        <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Status Seller</h2>
                        <p class="text-lg font-bold text-gray-900 uppercase">{{ auth()->user()->seller_status === 'none' ? 'Belum Ajukan' : auth()->user()->seller_status }}</p>
                    </div>
                </div>

                <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-6 bg-gray-50">
                    <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Toko</h2>
                    <p class="text-lg font-bold text-gray-900">{{ auth()->user()->store ? auth()->user()->store->name : 'Belum memiliki toko' }}</p>
                    @if(auth()->user()->store)
                        <p class="text-sm text-gray-500 mt-2">Status toko: {{ auth()->user()->store->status ?? 'pending' }}</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-8 bg-primary-50">
                <h2 class="text-2xl font-black text-gray-900 mb-4">Ubah ke Seller</h2>
                @if(auth()->user()->is_seller)
                    <p class="text-gray-600 mb-6">Akun Anda sudah disetujui sebagai seller. Anda dapat langsung menuju dashboard seller untuk mengelola toko dan produk.</p>
                    <a href="{{ route('seller.dashboard') }}" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">Buka Seller Dashboard</a>
                @else
                    <p class="text-gray-600 mb-6">Anda tetap bisa belanja seperti biasa. Aktifkan mode seller kapan saja untuk mulai berjualan tanpa ganti akun.</p>
                    <form method="POST" action="{{ route('seller.activate') }}" class="space-y-3">
                        @csrf
                        <input type="text" name="store_name" class="w-full rounded-2xl border border-gray-200 bg-white px-5 py-3 font-semibold focus:border-primary-600 focus:outline-none" placeholder="Nama toko (opsional)">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">Jadi Penjual</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
