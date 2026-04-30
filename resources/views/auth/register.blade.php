@extends('layouts.app')
@section('title', 'Daftar Akun — Arradea')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                <div class="w-10 h-10 rounded-2xl bg-[#72bf77] flex items-center justify-center shadow-lg shadow-green-300/30">
                    <span class="text-white font-black">A</span>
                </div>
                <span class="text-2xl font-black text-gray-900">Arradea<span class="text-[#72bf77]">.</span></span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Buat akun baru</h1>
            <p class="mt-2 text-gray-500 text-sm">Bergabung dengan komunitas Arradea Marketplace</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-green-100/30 border border-green-100/50 p-8">

            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-[#f0faf1] border border-green-200/60 rounded-2xl text-sm text-green-800 font-medium">
                <svg class="w-4 h-4 text-[#72bf77] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Khusus untuk warga Komplek Arradea
            </div>

            @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="register-form" method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
                        class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all @error('name') border-red-300 bg-red-50 @enderror"
                        placeholder="Nama lengkap Anda">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nomor WhatsApp</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required autocomplete="tel"
                        class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all @error('phone') border-red-300 bg-red-50 @enderror"
                        placeholder="08123456789">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password-input" required
                                class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl px-4 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all @error('password') border-red-300 bg-red-50 @enderror"
                                placeholder="••••••••">
                            <button type="button" id="toggle-pw" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                        {{-- Password strength --}}
                        <div class="mt-2 flex gap-1" id="strength-bars">
                            <div class="h-1 flex-1 rounded-full bg-gray-200 transition-colors" id="bar1"></div>
                            <div class="h-1 flex-1 rounded-full bg-gray-200 transition-colors" id="bar2"></div>
                            <div class="h-1 flex-1 rounded-full bg-gray-200 transition-colors" id="bar3"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Konfirmasi</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="pw-confirm" required
                                class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl px-4 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all"
                                placeholder="••••••••">
                            <span id="match-icon" class="absolute right-3 top-3 hidden">
                                <svg class="w-4 h-4 text-[#72bf77]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                    </div>
                </div>

                <div id="location-status" class="flex items-center gap-2.5 p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-500 font-medium">
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="location-text">Mengambil lokasi untuk verifikasi area...</span>
                </div>

                <p class="text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-xl p-3">
                    💡 Semua akun baru otomatis menjadi <strong>pembeli</strong>. Buka toko bisa dilakukan setelah akun aktif.
                </p>

                <button type="submit" id="submit-btn"
                    class="w-full h-12 rounded-xl font-bold text-sm text-white transition-all hover:opacity-90 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                    style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                    <span>Daftar & Verifikasi WhatsApp</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-[#72bf77] hover:text-[#3fa348] transition">Masuk sekarang</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Toggle password
    document.getElementById('toggle-pw')?.addEventListener('click', function(){
        const input = document.getElementById('password-input');
        input.type = input.type === 'password' ? 'text' : 'password';
    });

    // Password strength
    const pwInput = document.getElementById('password-input');
    pwInput?.addEventListener('input', function(){
        const val = this.value;
        const len = val.length;
        const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
        let strength = 0;
        if(len >= 6) strength++;
        if(len >= 10 && /[A-Z]/.test(val)) strength++;
        if(len >= 10 && /[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) strength++;
        const colors = ['#ef4444','#f59e0b','#72bf77'];
        bars.forEach((b,i) => { b.style.background = i < strength ? colors[strength-1] : '#e5e7eb'; });
    });

    // Password match
    const confirm = document.getElementById('pw-confirm');
    confirm?.addEventListener('input', function(){
        const match = this.value === document.getElementById('password-input').value && this.value.length > 0;
        document.getElementById('match-icon').classList.toggle('hidden', !match);
        this.style.borderColor = this.value.length > 0 ? (match ? '#72bf77' : '#ef4444') : '';
    });

    // Geolocation
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locText = document.getElementById('location-text');
    if(latInput?.value && lngInput?.value){ locText && (locText.textContent = '✓ Lokasi berhasil didapatkan.'); return; }
    if(!navigator.geolocation){ locText && (locText.textContent = 'Geolocation tidak tersedia. Anda tetap bisa mendaftar.'); return; }
    navigator.geolocation.getCurrentPosition(
        pos => {
            if(latInput) latInput.value = pos.coords.latitude.toFixed(7);
            if(lngInput) lngInput.value = pos.coords.longitude.toFixed(7);
            if(locText) locText.textContent = '✓ Lokasi berhasil didapatkan.';
        },
        () => { if(locText) locText.textContent = 'Lokasi tidak tersedia. Anda tetap bisa mendaftar.'; },
        { enableHighAccuracy:true, timeout:10000, maximumAge:300000 }
    );
});
</script>
@endpush
@endsection
