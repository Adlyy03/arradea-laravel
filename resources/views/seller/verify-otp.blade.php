@extends('layouts.app')

@section('title', 'Verifikasi OTP Seller — Arradea')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">

        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-yellow-100 rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10">

            <div class="text-center mb-10">
                <div class="text-6xl mb-6">🏪</div>
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-4 tracking-tighter">
                    Verifikasi <span class="text-primary-600 underline underline-offset-8">Upgrade Seller</span>.
                </h1>
                <p class="text-gray-400 font-bold text-base lg:text-lg mt-4">
                    Kami kirim kode OTP 6 digit ke WhatsApp kamu. Masukkan di bawah untuk lanjut jadi Seller.
                </p>
            </div>

            {{-- Nomor HP --}}
            <div class="mb-6 p-4 lg:p-6 bg-primary-50 border border-primary-100 rounded-2xl flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-primary-400">OTP dikirim ke</p>
                    <p class="text-primary-900 font-black text-lg">{{ $user->phone }}</p>
                </div>
            </div>

            {{-- Status sukses kirim ulang --}}
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-green-800 font-bold text-sm">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Error OTP --}}
            @if ($errors->any())
                <div class="mb-6 p-4 lg:p-6 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
                    <span class="text-xl shrink-0">❌</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 font-bold text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Form OTP --}}
            <form method="POST" action="{{ route('seller.verify-otp.submit') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="code" class="block text-sm font-black uppercase tracking-widest text-gray-500">
                        Kode OTP (6 Digit)
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        maxlength="6"
                        placeholder="000000"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        class="w-full h-20 lg:h-24 text-center text-4xl lg:text-5xl font-black tracking-[0.5em] border-2 border-gray-200 rounded-2xl focus:border-primary-600 focus:outline-none transition-all @error('code') border-red-400 bg-red-50 @enderror"
                        value="{{ old('code') }}"
                        required
                        autofocus
                        pattern="[0-9]{6}"
                    >
                </div>

                <button type="submit" class="w-full h-16 lg:h-20 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-lg lg:text-2xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verifikasi & Ajukan ke Admin
                </button>
            </form>

            {{-- Kirim ulang OTP --}}
            <form method="POST" action="{{ route('seller.verify-otp.resend') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full h-12 lg:h-14 border-2 border-gray-300 text-gray-600 rounded-2xl font-bold text-sm lg:text-base hover:border-primary-500 hover:text-primary-700 transition-all">
                    📨 Kirim Ulang Kode OTP
                </button>
            </form>

            <p class="text-center mt-6 text-gray-400 font-bold text-sm">
                Batal jadi seller?
                <a href="{{ route('profile') }}" class="text-primary-600 font-black hover:underline underline-offset-4">
                    Kembali ke Profil
                </a>
            </p>

        </div>
    </div>
</div>

<script>
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            if (this.value.length === 6) {
                setTimeout(() => this.closest('form').submit(), 300);
            }
        });
    }
</script>
@endsection
