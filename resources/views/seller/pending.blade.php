@extends('layouts.app')

@section('title', 'Menunggu Approval Seller — Arradea')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">

        <div class="absolute -top-12 -right-12 w-48 h-48 bg-orange-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-yellow-100 rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10 text-center">

            <div class="mb-8">
                <div class="text-7xl mb-6 animate-bounce">🏪</div>
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-3 tracking-tighter">
                    Pengajuan <span class="text-orange-500">Seller Dikirim!</span>
                </h1>
                <p class="text-gray-400 font-bold text-base lg:text-lg mt-4">
                    Kamu masih bisa berbelanja seperti biasa sambil nunggu persetujuan.
                </p>
            </div>

            {{-- Info toko --}}
            @if($user->store)
            <div class="p-5 bg-orange-50 border-2 border-orange-200 rounded-3xl mb-6 text-left">
                <p class="text-[10px] font-black uppercase text-orange-400 mb-2 tracking-widest">Toko yang Diajukan</p>
                <p class="text-gray-900 font-black text-xl">{{ $user->store->name }}</p>
                @if($user->store->description)
                    <p class="text-gray-500 text-sm mt-1">{{ $user->store->description }}</p>
                @endif
            </div>
            @endif

            {{-- Status card --}}
            <div class="p-6 lg:p-8 bg-blue-50 border-2 border-blue-200 rounded-3xl mb-6 text-left">
                <p class="text-gray-700 font-bold text-base lg:text-lg mb-3">
                    🎉 Pengajuan seller-mu sudah kami terima!
                </p>
                <p class="text-gray-600 font-bold text-sm lg:text-base">
                    Admin kami sedang mereview pengajuanmu. Setelah disetujui, kamu akan mendapat akses <span class="text-blue-600 font-black">Dashboard Seller</span> dan bisa langsung mulai berjualan.
                </p>
            </div>

            {{-- Estimasi --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-5 mb-8">
                <p class="text-amber-900 font-bold text-sm">
                    ⏱️ <strong>Estimasi waktu:</strong> Admin biasanya menyetujui dalam <strong>1–2 jam</strong>.
                    Kami akan kirim notifikasi ke WhatsApp saat sudah disetujui.
                </p>
            </div>

            {{-- Progress steps --}}
            <div class="space-y-3 mb-8 text-left">
                <div class="flex items-center gap-3 p-3 rounded-xl">
                    <span class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-black flex items-center justify-center shrink-0">✓</span>
                    <p class="text-gray-600 font-bold text-sm">Isi data toko</p>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl">
                    <span class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-black flex items-center justify-center shrink-0">✓</span>
                    <p class="text-gray-600 font-bold text-sm">Verifikasi OTP via WhatsApp</p>
                </div>
                <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-xl border border-orange-200">
                    <span class="w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-black flex items-center justify-center shrink-0 animate-pulse">3</span>
                    <p class="text-orange-900 font-bold text-sm">Menunggu persetujuan admin <em>(kamu di sini)</em></p>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl opacity-40">
                    <span class="w-7 h-7 rounded-full bg-gray-300 text-white text-xs font-black flex items-center justify-center shrink-0">4</span>
                    <p class="text-gray-500 font-bold text-sm">Akses Seller Dashboard & mulai berjualan 🛒</p>
                </div>
            </div>

            <a href="{{ route('buyer.dashboard') }}" class="w-full h-14 lg:h-20 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-base lg:text-xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2">
                Kembali Belanja Dulu →
            </a>

        </div>
    </div>
</div>
@endsection
