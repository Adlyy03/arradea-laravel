@extends('layouts.app')
@section('title', 'Masuk — Arradea')

@section('content')
@php $captchaSiteKey = config('services.recaptcha.site_key'); @endphp

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo & Heading --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                <div class="w-10 h-10 rounded-2xl bg-[#72bf77] flex items-center justify-center shadow-lg shadow-green-300/30">
                    <span class="text-white font-black">A</span>
                </div>
                <span class="text-2xl font-black text-gray-900">Arradea<span class="text-[#72bf77]">.</span></span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Selamat datang kembali</h1>
            <p class="mt-2 text-gray-500 text-sm">Masuk ke akun Arradea Anda</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-green-100/30 border border-green-100/50 p-8">

            @if($errors->any())
                <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-medium">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-5 p-4 bg-amber-50 border border-amber-100 rounded-2xl text-amber-700 text-sm font-medium">
                    {{ session('warning') }}
                </div>
            @endif

            <form id="login-form" method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nomor HP</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all placeholder-gray-300 @error('phone') border-red-300 bg-red-50 @enderror"
                            placeholder="08123456789">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kata Sandi</label>
                        <a href="#" class="text-xs font-semibold text-[#72bf77] hover:text-[#3fa348] transition">Lupa sandi?</a>
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input type="password" name="password" id="password-input" required
                            class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-12 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#72bf77]/40 focus:border-[#72bf77]/60 transition-all placeholder-gray-300"
                            placeholder="••••••••">
                        <button type="button" id="toggle-pw" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 transition">
                            <svg id="eye-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded-md border-gray-300 text-[#72bf77] focus:ring-[#72bf77]/40">
                    <label for="remember" class="text-sm text-gray-600 font-medium">Ingat saya</label>
                </div>

                @if($captchaSiteKey)
                <div>
                    <div class="flex justify-center">
                        <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
                    </div>
                    @if($errors->has('captcha') || $errors->has('g-recaptcha-response'))
                        <p class="text-xs text-red-500 mt-1.5">{{ $errors->first('captcha') ?: $errors->first('g-recaptcha-response') }}</p>
                    @endif
                </div>
                @endif

                <button type="submit" class="w-full h-12 rounded-xl font-bold text-sm text-white transition-all hover:opacity-90 hover:-translate-y-0.5 active:translate-y-0" style="background:#72bf77;box-shadow:0 4px 20px rgba(114,191,119,.4)">
                    Masuk Sekarang
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-[#72bf77] hover:text-[#3fa348] transition">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>

@if($captchaSiteKey)
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@push('scripts')
<script>
document.getElementById('toggle-pw').addEventListener('click', function(){
    const input = document.getElementById('password-input');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    eyeOpen.classList.toggle('hidden', isPassword);
    eyeClosed.classList.toggle('hidden', !isPassword);
});
</script>
@endpush
@endsection
