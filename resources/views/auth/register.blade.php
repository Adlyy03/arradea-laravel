@extends('layouts.app')
@section('title', 'Daftar Akun — Arradea')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
<<<<<<< HEAD
                <img src="{{ asset('images/arradea.jpeg') }}" alt="Arradea" class="w-10 h-10 rounded-2xl object-cover shadow-lg shadow-green-300/30">
=======
                <div class="w-10 h-10 rounded-2xl bg-[#72bf77] flex items-center justify-center shadow-lg shadow-green-300/30">
                    <img src="/icons/logo-arradea.png" alt="Arradea" class="w-6 h-6 object-cover">
                </div>
>>>>>>> 1688c02551a4c3a5c36573e09b0fed8b8d385f24
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
                        <p id="pw-hint" class="text-[10px] text-gray-400 mt-1">Min. 8 karakter, huruf besar, angka, tanpa spasi</p>
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

    // Block spaces in password fields in real-time
    ['password-input', 'pw-confirm'].forEach(function(id) {
        document.getElementById(id)?.addEventListener('keydown', function(e) {
            if (e.key === ' ') e.preventDefault();
        });
        document.getElementById(id)?.addEventListener('input', function() {
            if (this.value.includes(' ')) {
                this.value = this.value.replace(/\s/g, '');
            }
        });
    });

    // Password strength meter
    const pwInput = document.getElementById('password-input');
    const pwHint  = document.getElementById('pw-hint');
    pwInput?.addEventListener('input', function(){
        const val = this.value;
        const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];

        const hasLen    = val.length >= 8;
        const hasUpper  = /[A-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const noSpace   = !/\s/.test(val);

        let strength = 0;
        if (hasLen && noSpace) strength++;
        if (hasUpper) strength++;
        if (hasNumber) strength++;

        const colors = ['#ef4444', '#f59e0b', '#72bf77'];
        bars.forEach((b, i) => {
            b.style.background = i < strength ? colors[strength - 1] : '#e5e7eb';
        });

        // Hint text
        const missing = [];
        if (!hasLen)    missing.push('min. 8 karakter');
        if (!hasUpper)  missing.push('huruf besar');
        if (!hasNumber) missing.push('angka');
        if (!noSpace)   missing.push('hapus spasi');

        if (pwHint) {
            if (missing.length === 0) {
                pwHint.textContent = '✓ Password kuat';
                pwHint.style.color = '#72bf77';
            } else {
                pwHint.textContent = 'Perlu: ' + missing.join(', ');
                pwHint.style.color = strength === 0 ? '#ef4444' : '#f59e0b';
            }
        }
    });

    // Password match indicator
    const confirm = document.getElementById('pw-confirm');
    confirm?.addEventListener('input', function(){
        const match = this.value === document.getElementById('password-input').value && this.value.length > 0;
        document.getElementById('match-icon').classList.toggle('hidden', !match);
        this.style.borderColor = this.value.length > 0 ? (match ? '#72bf77' : '#ef4444') : '';
    });

    // Block form submit if password has spaces (double safety)
    document.getElementById('register-form')?.addEventListener('submit', function(e) {
        const pw = document.getElementById('password-input').value;
        if (/\s/.test(pw)) {
            e.preventDefault();
            if (pwHint) { pwHint.textContent = '❌ Password tidak boleh mengandung spasi!'; pwHint.style.color = '#ef4444'; }
            document.getElementById('password-input').focus();
            return;
        }

        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        if (!lat || !lng) {
            e.preventDefault();
            alert('Anda wajib mengizinkan akses lokasi di browser untuk mendaftar!');
        }
    });

    // Geolocation
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locText  = document.getElementById('location-text');
    const submitBtn = document.getElementById('submit-btn');

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    if (latInput?.value && lngInput?.value) { 
        if (locText) locText.textContent = '✓ Lokasi berhasil didapatkan.'; 
        if (submitBtn) { 
            submitBtn.disabled = false; 
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); 
        }
        return; 
    }
    if (!navigator.geolocation) { 
        if (locText) {
            locText.textContent = '❌ Browser tidak mendukung lokasi.';
            locText.classList.remove('text-gray-500');
            locText.classList.add('text-red-500');
        }
        return; 
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            if (latInput) latInput.value = pos.coords.latitude.toFixed(7);
            if (lngInput) lngInput.value = pos.coords.longitude.toFixed(7);
            if (locText)  locText.textContent = '✓ Lokasi berhasil didapatkan.';
            if (submitBtn) { 
                submitBtn.disabled = false; 
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); 
            }
        },
        () => { 
            if (locText) {
                locText.textContent = '❌ Wajib izinkan akses lokasi!'; 
                locText.classList.remove('text-gray-500');
                locText.classList.add('text-red-500');
            }
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
    );
});
</script>
@endpush
@endsection
