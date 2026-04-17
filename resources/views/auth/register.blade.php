@extends('layouts.app')

@section('title', 'Daftar Akun — Arradea')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">

        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-accent rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10">

            <div class="text-center mb-12">
                <div class="text-5xl mb-4">🏡</div>
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-4 tracking-tighter">
                    Buka <span class="text-primary-600 underline underline-offset-8">Akun</span> Baru.
                </h1>
                <p class="text-gray-400 font-bold text-base lg:text-lg">
                    Lengkapi data diri untuk mulai belanja di Arradea.
                </p>
            </div>

            <div class="mb-6 p-4 bg-primary-50 border border-primary-100 rounded-2xl text-primary-800 font-bold text-xs lg:text-sm">
                📍 Aplikasi ini hanya untuk warga Komplek Arradea.
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 lg:p-6 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 font-bold text-sm">❌ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="register-form" method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                {{-- Nama --}}
                <div class="space-y-2">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name"
                        class="w-full h-14 lg:h-20 bg-gray-50 border-2 border-transparent rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-0 focus:border-primary-500 font-black text-base lg:text-xl transition-all @error('name') border-red-400 bg-red-50 @enderror"
                        placeholder="Contoh: Budi Santoso"
                    >
                    @error('name')
                        <p class="text-red-500 text-xs font-bold pl-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor HP --}}
                <div class="space-y-2">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">
                        Nomor WhatsApp Aktif
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        value="{{ old('phone') }}"
                        required
                        autocomplete="tel"
                        class="w-full h-14 lg:h-20 bg-gray-50 border-2 border-transparent rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-0 focus:border-primary-500 font-black text-base lg:text-xl transition-all @error('phone') border-red-400 bg-red-50 @enderror"
                        placeholder="Contoh: 08123456789"
                    >
                    @error('phone')
                        <p class="text-red-500 text-xs font-bold pl-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">
                            Kata Sandi
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            class="w-full h-14 lg:h-20 bg-gray-50 border-2 border-transparent rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-0 focus:border-primary-500 font-black text-base lg:text-xl transition-all @error('password') border-red-400 bg-red-50 @enderror"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="text-red-500 text-xs font-bold pl-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">
                            Konfirmasi Sandi
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full h-14 lg:h-20 bg-gray-50 border-2 border-transparent rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-0 focus:border-primary-500 font-black text-base lg:text-xl transition-all"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <p class="text-[10px] lg:text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-2xl p-4">
                    💡 Semua pendaftaran baru akan otomatis menjadi <strong>pembeli</strong>. Buka toko bisa dilakukan setelah akun aktif.
                </p>

                <p id="location-status" class="text-[10px] lg:text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded-2xl p-4">
                    📍 Sistem mencoba mengambil lokasi Anda untuk verifikasi area layanan.
                </p>

                <div class="pt-4">
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full h-16 lg:h-24 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-lg lg:text-2xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3"
                    >
                        <span>Daftar & Verifikasi WhatsApp</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </button>

                    <p class="text-center mt-8 text-gray-400 font-bold text-sm lg:text-base">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary-600 font-black hover:underline underline-offset-4 decoration-primary-300">
                            Masuk Sekarang
                        </a>
                    </p>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const status = document.getElementById('location-status');

        if (!latInput || !lngInput || !status) {
            return;
        }

        if (latInput.value && lngInput.value) {
            status.textContent = 'Lokasi berhasil didapatkan.';
            return;
        }

        if (!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung Geolocation. Anda tetap bisa melanjutkan pendaftaran.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                latInput.value = position.coords.latitude.toFixed(7);
                lngInput.value = position.coords.longitude.toFixed(7);
                status.textContent = 'Lokasi berhasil didapatkan.';
            },
            function () {
                status.textContent = 'Lokasi tidak tersedia. Anda tetap bisa melanjutkan pendaftaran.';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000,
            }
        );
    });
</script>
@endsection
