@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center h-full">
    <div class="w-full max-w-xl bg-white rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">
        
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-accent rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10">
            @php
                $captchaSiteKey = config('services.recaptcha.site_key');
            @endphp

            <div class="text-center mb-16">
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-4 tracking-tighter">Selamat <span class="text-primary-600 underline underline-offset-8">Datang</span> Kembali.</h1>
                <p class="text-gray-400 font-bold text-lg">Masuk untuk melanjutkan pengalaman belanja premium Anda di Arradea.</p>
            </div>

            @if($errors->any())
                <div class="mb-10 p-6 bg-red-50 border border-red-100 rounded-[2.5rem] text-red-600 font-bold text-sm shadow-sm animate-shake">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-8 p-5 bg-amber-50 border border-amber-100 rounded-2xl text-amber-700 font-bold text-xs lg:text-sm">
                    {{ session('warning') }}
                </div>
            @endif

            <form id="login-form" method="POST" action="/web/login" class="space-y-6 lg:space-y-8">
                @csrf
                <div class="space-y-4">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Nomor HP</label>
                    <input type="phone" name="phone" value="{{ old('phone') }}" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="081234567890">
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center px-4">
                        <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Kata Sandi</label>
                        <a href="#" class="text-[9px] lg:text-[10px] font-black text-primary-600 hover:text-primary-700 uppercase tracking-widest">Lupa Sandi?</a>
                    </div>
                    <input type="password" name="password" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="••••••••">
                </div>

                <div class="flex items-center space-x-3 px-6">
                    <input type="checkbox" name="remember" id="remember" class="w-5 h-5 lg:w-6 lg:h-6 rounded-lg lg:rounded-xl border-gray-200 text-primary-600 focus:ring-primary-600">
                    <label for="remember" class="text-[10px] lg:text-xs font-black text-gray-500 uppercase tracking-widest">Ingat Sesi Saya</label>
                </div>

                @if($captchaSiteKey)
                    <div class="px-4 lg:px-6 space-y-3">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-600">
                            <svg class="w-5 h-5 text-primary-600 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 2l7 4v6c0 5-3.4 9.7-7 10-3.6-.3-7-5-7-10V6l7-4Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M9.5 12.2l1.9 1.9 3.9-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <p class="text-[10px] lg:text-xs font-black uppercase tracking-widest text-gray-500">Verifikasi captcha aktif</p>
                                <p class="text-[10px] lg:text-xs text-gray-400 font-medium">Centang kotak di bawah untuk melanjutkan.</p>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
                        </div>
                        @if($errors->has('captcha') || $errors->has('recaptcha_token'))
                            <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 font-bold text-xs lg:text-sm">
                                {{ $errors->first('captcha') ?: $errors->first('g-recaptcha-response') }}
                            </div>
                        @endif
                    </div>
                @endif

                <button type="submit" class="w-full h-16 lg:h-24 bg-primary-900 text-white rounded-2xl lg:rounded-[2.5rem] font-black text-lg lg:text-2xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95">
                    Masuk Sekarang
                </button>

                <div class="text-center pt-10">
                    <p class="text-gray-400 font-bold text-lg">Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-primary-600 font-black hover:underline underline-offset-[1.5rem] decoration-primary-300">Daftar Akun Baru</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

@if($captchaSiteKey)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endsection
