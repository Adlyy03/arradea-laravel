@extends('layouts.dashboard')
@section('title', 'Buat Keluhan')
@section('page_title', 'Buat Keluhan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-xl font-black text-gray-900 mb-6">Sampaikan Keluhan Anda</h2>
        
        <form action="{{ route('complaints.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Subjek Keluhan</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-sage/30 focus:border-sage/50 transition"
                    placeholder="Contoh: Produk tidak sesuai deskripsi">
                @error('subject')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Pesan Keluhan</label>
                <textarea name="message" rows="6" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-sage/30 focus:border-sage/50 transition"
                    placeholder="Jelaskan keluhan Anda secara detail...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                    Kirim Keluhan
                </button>
                <a href="{{ route('complaints.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
