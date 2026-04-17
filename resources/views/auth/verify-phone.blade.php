@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center h-full">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">

        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-accent rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10">

            <div class="text-center mb-16">
                <div class="text-6xl mb-6">📱</div>
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-4 tracking-tighter">Verifikasi <span class="text-primary-600 underline underline-offset-8">Nomor HP</span> Dulu.</h1>
                <p class="text-gray-400 font-bold text-lg mt-6">Kami sudah kirim link verifikasi ke WhatsApp kamu. Klik link-nya untuk melanjutkan.</p>
            </div>

            {{-- Nomor HP user --}}
            <div class="mb-6 p-4 lg:p-6 bg-primary-50 border border-primary-100 rounded-2xl flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-primary-400">Nomor terdaftar</p>
                    <p class="text-primary-900 font-black text-lg">{{ auth()->user()->phone }}</p>
                </div>
            </div>

            {{-- Status sukses kirim --}}
            @if(session('status'))
                <div class="mb-6 p-4 lg:p-6 bg-green-50 border border-green-100 rounded-2xl text-green-700 font-bold text-sm">
                    ✅ {{ session('status') }}
                </div>
            @endif

            {{-- Tombol kirim ulang --}}
            <form method="POST" action="{{ route('verification.phone.send') }}">
                @csrf
                <button type="submit" class="w-full h-16 lg:h-24 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-lg lg:text-2xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95">
                    Kirim Ulang Link WhatsApp
                </button>
            </form>

            <p class="text-center mt-8 text-gray-400 font-bold text-sm lg:text-base">
                Nomor salah? 
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-primary-600 font-black hover:underline underline-offset-4">
                    Logout & daftar ulang
                </a>
            </p>

            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

        </div>
    </div>
</div>
@endsection