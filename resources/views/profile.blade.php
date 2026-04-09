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
                        <h2 class="text-sm uppercase tracking-widest text-gray-400 font-black mb-4">Peran</h2>
                        <p class="text-lg font-bold text-primary-700 uppercase">{{ auth()->user()->role }}</p>
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
                @if(auth()->user()->role === 'seller')
                    <p class="text-gray-600 mb-6">Akun Anda sudah disetujui sebagai seller. Anda dapat langsung menuju dashboard seller untuk mengelola toko dan produk.</p>
                    <a href="{{ route('seller.dashboard') }}" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">Buka Seller Dashboard</a>
                @elseif(auth()->user()->seller_status === 'pending')
                    <p class="text-gray-600 mb-6">Permohonan seller Anda sedang ditinjau oleh admin. Silakan tunggu persetujuan atau cek kembali nanti.</p>
                    <a href="{{ route('seller.apply') }}" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-white text-gray-900 border border-gray-200 font-black px-6 py-4 hover:bg-gray-100 transition">Lihat Status Aplikasi</a>
                @elseif(auth()->user()->seller_status === 'rejected')
                    <p class="text-gray-600 mb-6">Permohonan seller Anda ditolak. Silakan perbarui data toko lalu ajukan kembali.</p>
                    <a href="{{ route('seller.apply') }}" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">Ajukan Kembali Seller</a>
                @else
                    <p class="text-gray-600 mb-6">Semua pendaftaran baru dibuat sebagai pembeli. Jika Anda ingin mulai berjualan di Arradea, daftarkan toko Anda melalui halaman aplikasi seller.</p>
                    <a href="{{ route('seller.apply') }}" class="w-full inline-flex items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">Daftar Jadi Seller</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
