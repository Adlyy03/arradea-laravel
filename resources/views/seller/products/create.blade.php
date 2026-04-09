@extends('layouts.dashboard')

@php $isEdit = isset($product); @endphp
@section('title', ($isEdit ? 'Edit Produk' : 'Tambah Produk') . ' - Arradea')
@section('page_title', ($isEdit ? 'Pembaruan Data Produk' : 'Input Katalog Baru'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 space-y-2">
            <p class="font-black text-red-700">❌ Ada kesalahan:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white p-8 lg:p-6 lg:p-12 rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 text-center lg:text-left">
        <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tighter leading-tight mb-4">
            {{ $isEdit ? 'Ubah Detail' : 'Input' }} <span class="text-primary-600">Produk</span>{{ $isEdit ? '' : ' Baru' }}.
        </h1>
        <p class="text-gray-500 text-sm lg:text-base font-medium">Berikan deskripsi menarik dan foto kualitas tinggi untuk jualan Anda.</p>
    </div>

    <!-- Form -->
    <form action="{{ $isEdit ? '/web/product/'.$product->id.'/update' : '/web/product/store' }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-8 lg:p-16 shadow-2xl border border-gray-100 space-y-8 lg:space-y-6 lg:space-y-12 relative overflow-hidden">
        @csrf
        @if($isEdit) @method('PUT') @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
            <!-- Product Name -->
            <div class="space-y-3 col-span-2">
                <label class="block text-[10px] font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $product->name : '') }}" required class="w-full h-14 lg:h-18 bg-gray-50 border-none rounded-2xl lg:rounded-2xl lg:rounded-3xl px-6 lg:px-8 focus:ring-2 focus:ring-primary-600 font-bold text-lg lg:text-xl transition-all {{ $errors->has('name') ? 'ring-2 ring-red-500' : '' }}" placeholder="Elite Hyper Sprint">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Price -->
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Harga (Rp) *</label>
                <input type="number" name="price" value="{{ old('price', $isEdit ? $product->price : '') }}" required class="w-full h-14 lg:h-18 bg-gray-50 border-none rounded-2xl lg:rounded-2xl lg:rounded-3xl px-6 lg:px-8 focus:ring-2 focus:ring-primary-600 font-bold text-lg lg:text-xl transition-all {{ $errors->has('price') ? 'ring-2 ring-red-500' : '' }}" placeholder="1500000">
                @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Stock -->
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Stok *</label>
                <input type="number" name="stock" value="{{ old('stock', $isEdit ? $product->stock : '') }}" required class="w-full h-14 lg:h-18 bg-gray-50 border-none rounded-2xl lg:rounded-2xl lg:rounded-3xl px-6 lg:px-8 focus:ring-2 focus:ring-primary-600 font-bold text-lg lg:text-xl transition-all {{ $errors->has('stock') ? 'ring-2 ring-red-500' : '' }}" placeholder="10">
                @error('stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="space-y-3 col-span-2">
                <label class="block text-[10px] font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full bg-gray-50 border-none rounded-2xl lg:rounded-2xl lg:rounded-3xl px-6 lg:px-8 py-5 lg:py-6 focus:ring-2 focus:ring-primary-600 font-bold text-base lg:text-lg transition-all {{ $errors->has('description') ? 'ring-2 ring-red-500' : '' }}" placeholder="Detail produk Anda...">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Image Upload -->
            <div class="space-y-3 col-span-2">
                <label class="block text-[10px] font-black text-gray-400 border-l-4 border-primary-600 pl-4 uppercase tracking-widest">Foto Produk</label>
                <div class="flex gap-6">
                    <!-- Preview Image -->
                    <div class="w-32 h-32 rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200 flex-shrink-0">
                        <img id="imagePreview" src="{{ $isEdit && $product->image ? $product->image : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200' }}" alt="preview" class="w-full h-full object-cover">
                    </div>
                    <!-- Input -->
                    <div class="flex-1 space-y-2">
                        <input type="file" name="image" accept="image/*" id="imageInput" class="w-full h-14 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl lg:rounded-2xl lg:rounded-3xl px-6 lg:px-8 py-4 focus:ring-2 focus:ring-primary-600 font-bold text-[10px] text-gray-500 cursor-pointer {{ $errors->has('image') ? 'ring-2 ring-red-500' : '' }}">
                        <p class="text-[10px] text-gray-400">Format: JPG, PNG, WEBP (Max 2MB)</p>
                        @if($isEdit && $product->image)
                            <p class="text-[10px] text-gray-500">Foto sekarang: <strong>Ada</strong>. Kosongkan input di atas untuk gunakan yang lama.</p>
                        @endif
                        @error('image') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-gray-50 flex flex-col sm:flex-row gap-4 relative z-10">
            <button type="submit" class="w-full h-16 bg-primary-600 text-white rounded-2xl font-black text-lg hover:bg-primary-700 shadow-xl shadow-primary-200 transition active:scale-95 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $isEdit ? 'Simpan Perubahan' : 'Publish Produk' }}
            </button>
            <a href="/seller/products" class="w-full h-16 bg-gray-50 text-gray-400 rounded-2xl font-black text-lg flex items-center justify-center hover:bg-gray-100 transition">Batal</a>
        </div>
    </form>
</div>

<script>
    // Image preview on file select
    document.getElementById('imageInput').addEventListener('change', function(e) {
        if(e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('imagePreview').src = event.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection
