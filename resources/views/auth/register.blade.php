@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20 flex justify-center items-center h-full">
    <div class="w-full max-w-2xl bg-white rounded-[4.5rem] p-8 lg:p-16 shadow-3xl border border-gray-100 relative overflow-hidden">
        
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply blur-3xl opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-accent rounded-full mix-blend-multiply blur-3xl opacity-20"></div>

        <div class="relative z-10">
            <div class="text-center mb-16">
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 leading-[0.9] mb-4 tracking-tighter">Buka <span class="text-primary-600 underline underline-offset-8">Akun</span> Baru.</h1>
                <p class="text-gray-400 font-bold text-lg">Lengkapi data diri Anda untuk memulai pengalaman belanja premium di Arradea.</p>
            </div>

            @if($errors->any())
                <div class="mb-10 p-6 bg-red-50 border border-red-100 rounded-[2.5rem] text-red-600 font-bold text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/web/register" class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-10">
                @csrf
                <div class="space-y-4 md:col-span-2">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="Contoh: Arradea Saputra">
                </div>

                <div class="space-y-4 md:col-span-2">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="yourname@gmail.com">
                </div>

                <div class="space-y-4 col-span-1">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Kata Sandi</label>
                    <input type="password" name="password" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="••••••••">
                </div>

                <div class="space-y-4 col-span-1">
                    <label class="block text-[10px] lg:text-xs font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Konfirmasi Sandi</label>
                    <input type="password" name="password_confirmation" required class="w-full h-14 lg:h-20 bg-gray-50 border-none rounded-2xl lg:rounded-[1.8rem] px-5 lg:px-10 focus:ring-4 focus:ring-primary-100 font-black text-base lg:text-xl transition-all" placeholder="••••••••">
                </div>

                <div class="space-y-4 md:col-span-2">
                    <p class="text-[10px] lg:text-sm text-gray-500 bg-gray-50 border border-gray-100 rounded-2xl lg:rounded-[2rem] p-4 lg:p-6">Semua pendaftaran baru akan otomatis menjadi <strong>pembeli</strong>. Jika ingin membuka toko sebagai seller, Anda dapat mengajukannya nanti melalui halaman profil setelah login.</p>
                </div>

                <div class="md:col-span-2 pt-6 lg:pt-10">
                    <button type="submit" class="w-full h-16 lg:h-24 bg-primary-900 text-white rounded-2xl lg:rounded-[2.8rem] font-black text-lg lg:text-2xl hover:bg-black shadow-3xl shadow-primary-900/10 transition-all transform hover:scale-[1.02] active:scale-95">
                        Daftar Akun Sekarang
                    </button>
                    <p class="text-center mt-8 lg:mt-12 text-gray-400 font-bold text-sm lg:text-lg">Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-primary-600 font-black hover:underline underline-offset-[1.5rem] decoration-primary-300">Masuk Sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
