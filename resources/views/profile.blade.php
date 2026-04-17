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
                <h2 class="text-2xl font-black text-gray-900 mb-4">Upgrade ke Seller</h2>

                @if(auth()->user()->is_seller)
                    {{-- Sudah jadi seller --}}
                    <p class="text-gray-600 mb-6">Akun kamu sudah aktif sebagai seller. Langsung kelola toko dan produkmu!</p>
                    <a href="{{ route('seller.dashboard') }}" class="w-full inline-flex items-center justify-center rounded-2xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">
                        🏪 Buka Seller Dashboard
                    </a>

                @elseif(auth()->user()->seller_otp_verified)
                    {{-- OTP sudah verify, nunggu admin --}}
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-2xl mb-4">
                        <p class="text-orange-800 font-bold text-sm">⏳ Pengajuan seller-mu sedang ditinjau admin. Sabar ya!</p>
                    </div>
                    <a href="{{ route('seller.pending') }}" class="w-full inline-flex items-center justify-center rounded-2xl bg-orange-500 text-white font-black px-6 py-4 hover:bg-orange-600 transition">
                        Lihat Status Pengajuan
                    </a>

                @elseif(auth()->user()->seller_status === 'pending' && !auth()->user()->seller_otp_verified)
                    {{-- Sudah isi form, OTP belum diverifikasi --}}
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-2xl mb-4">
                        <p class="text-yellow-800 font-bold text-sm">📱 Kamu belum menyelesaikan verifikasi OTP. Cek WhatsApp-mu!</p>
                    </div>
                    <a href="{{ route('seller.verify-otp') }}" class="w-full inline-flex items-center justify-center rounded-2xl bg-yellow-500 text-white font-black px-6 py-4 hover:bg-yellow-600 transition">
                        Lanjut Verifikasi OTP
                    </a>

                @elseif(auth()->user()->seller_status === 'rejected')
                    {{-- Ditolak --}}
                    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl mb-4">
                        <p class="text-red-800 font-bold text-sm">❌ Pengajuan seller-mu sebelumnya ditolak. Kamu bisa mengajukan ulang.</p>
                    </div>
                    <a href="{{ route('seller.apply') }}" class="w-full inline-flex items-center justify-center rounded-2xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">
                        Ajukan Ulang Jadi Seller
                    </a>

                @else
                    {{-- Belum pernah ajukan --}}
                    <p class="text-gray-600 mb-6">Kamu tetap bisa belanja seperti biasa. Aktifkan mode seller untuk mulai berjualan di Arradea!</p>
                    <a href="{{ route('seller.apply') }}" class="w-full inline-flex items-center justify-center rounded-2xl bg-primary-700 text-white font-black px-6 py-4 hover:bg-primary-800 transition">
                        🚀 Ajukan Jadi Seller
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
