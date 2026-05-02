@extends('layouts.dashboard')
@section('title', 'Edit Produk')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}" 
           class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Edit Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi produk {{ $product->name }}</p>
        </div>
    </div>

    {{-- Product Info Card --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100 p-5">
        <div class="flex items-start gap-4">
            <img src="{{ $product->image }}" 
                 alt="{{ $product->name }}"
                 class="w-20 h-20 rounded-xl object-cover shadow-md"
                 onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
            <div class="flex-1">
                <h3 class="text-lg font-black text-gray-900">{{ $product->name }}</h3>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                    <span class="flex items-center gap-1 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <strong>Toko:</strong> {{ $product->store->name ?? 'N/A' }}
                    </span>
                    @if($product->store && $product->store->user)
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-600">
                        <strong>Pemilik:</strong> {{ $product->store->user->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $product->name) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('name') border-red-300 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Price --}}
                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="{{ old('price', $product->price) }}"
                               min="0"
                               step="0.01"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('price') border-red-300 @enderror"
                               required>
                    </div>
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Discount --}}
                <div>
                    <label for="discount_percent" class="block text-sm font-bold text-gray-700 mb-2">
                        Diskon (%)
                    </label>
                    <input type="number" 
                           id="discount_percent" 
                           name="discount_percent" 
                           value="{{ old('discount_percent', $product->discount_percent) }}"
                           min="0"
                           max="100"
                           step="0.01"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('discount_percent') border-red-300 @enderror">
                    @error('discount_percent')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Stock --}}
                <div>
                    <label for="stock" class="block text-sm font-bold text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           value="{{ old('stock', $product->stock) }}"
                           min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('stock') border-red-300 @enderror"
                           required>
                    @error('stock')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2">
                        Kategori
                    </label>
                    <select id="category_id" 
                            name="category_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('category_id') border-red-300 @enderror">
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('description') border-red-300 @enderror"
                          placeholder="Deskripsi produk...">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-bold">Catatan Admin</p>
                    <p class="mt-1">Perubahan akan langsung terlihat oleh pembeli. Pastikan informasi yang dimasukkan sudah benar.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
                        style="background:#72bf77">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
