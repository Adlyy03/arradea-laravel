@extends('layouts.app')

@section('title', 'Menunggu Persetujuan Admin — Arradea')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">

        <div class="absolute -top-12 -right-12 w-48 h-48 bg-green-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-blue-100 rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10 text-center">

            {{-- Icon & Heading --}}
            <div class="mb-8">
                <div class="text-7xl mb-6 animate-bounce">✅</div>
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-3 tracking-tighter">
                    Nomor HP <span class="text-green-600">Terverifikasi!</span>
                </h1>
                <p class="text-gray-400 font-bold text-base lg:text-lg mt-4">
                    Satu langkah lagi sebelum kamu bisa mulai belanja.
                </p>
            </div>

            {{-- Status card --}}
            <div class="p-6 lg:p-8 bg-blue-50 border-2 border-blue-200 rounded-3xl mb-6 text-left">
                <p class="text-gray-700 font-bold text-base lg:text-lg mb-3">
                    🎉 Terima kasih sudah mendaftar di <strong>Arradea</strong>!
                </p>
                <p class="text-gray-600 font-bold text-sm lg:text-base">
                    Akun kamu sedang menunggu
                    <span class="text-blue-600 font-black">persetujuan dari admin</span>.
                    Setelah disetujui, kamu bisa langsung login dan mulai belanja.
                </p>
            </div>

            {{-- Nomor terdaftar --}}
            @if (session('register_phone'))
                <div class="flex items-center gap-4 p-4 lg:p-5 bg-gray-50 border border-gray-100 rounded-2xl mb-6">
                    <div class="text-2xl">📱</div>
                    <div class="text-left">
                        <p class="text-gray-400 text-xs font-black uppercase tracking-widest">Nomor terdaftar</p>
                        <p class="text-gray-900 font-black text-lg">{{ session('register_phone') }}</p>
                    </div>
                </div>
            @endif

            {{-- Estimasi waktu --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-5 mb-8">
                <p class="text-amber-900 font-bold text-sm">
                    ⏱️ <strong>Estimasi waktu:</strong> Admin biasanya menyetujui dalam <strong>1–2 jam</strong>.
                    Kami akan kirim notifikasi ke WhatsApp kamu saat sudah disetujui.
                </p>
            </div>

            {{-- Langkah berikutnya --}}
            <div class="space-y-3 mb-8 text-left">
                <div class="flex items-center gap-3 p-3 rounded-xl">
                    <span class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-black flex items-center justify-center shrink-0">✓</span>
                    <p class="text-gray-600 font-bold text-sm">Daftar akun</p>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl">
                    <span class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-black flex items-center justify-center shrink-0">✓</span>
                    <p class="text-gray-600 font-bold text-sm">Verifikasi nomor HP via WhatsApp</p>
                </div>
                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <span class="w-7 h-7 rounded-full bg-blue-500 text-white text-xs font-black flex items-center justify-center shrink-0 animate-pulse">3</span>
                    <p class="text-blue-800 font-bold text-sm">Menunggu persetujuan admin <em>(proses ini)</em></p>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl opacity-40">
                    <span class="w-7 h-7 rounded-full bg-gray-300 text-white text-xs font-black flex items-center justify-center shrink-0">4</span>
                    <p class="text-gray-500 font-bold text-sm">Login & mulai belanja 🛒</p>
                </div>
            </div>

            <a
                href="{{ route('login') }}"
                class="w-full h-14 lg:h-20 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-base lg:text-xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2"
            >
                Sudah disetujui? Login Sekarang →
            </a>

            <p class="text-center mt-6 text-gray-400 font-bold text-sm">
                Punya pertanyaan?
                <a href="https://wa.me/6285123456789" target="_blank" class="text-green-600 font-black hover:underline underline-offset-4">
                    Hubungi admin via WhatsApp
                </a>
            </p>

        </div>
    </div>
</div>
@endsection
