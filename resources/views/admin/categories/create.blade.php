@extends('layouts.dashboard')
@section('title', 'Tambah Kategori Baru')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" 
           class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Tambah Kategori Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Buat kategori baru untuk mengorganisir produk</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('name') border-red-300 @enderror"
                       placeholder="Contoh: Makanan & Minuman"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-sm font-bold text-gray-700 mb-2">
                    Slug (URL)
                </label>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       value="{{ old('slug') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('slug') border-red-300 @enderror"
                       placeholder="Otomatis dari nama (opsional)">
                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk generate otomatis dari nama</p>
                @error('slug')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('description') border-red-300 @enderror"
                          placeholder="Deskripsi singkat tentang kategori ini">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Parent Category --}}
            <div>
                <label for="parent_id" class="block text-sm font-bold text-gray-700 mb-2">
                    Parent Kategori
                </label>
                <select id="parent_id" 
                        name="parent_id"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('parent_id') border-red-300 @enderror">
                    <option value="">— Tidak Ada (Kategori Utama) —</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih parent jika ini adalah sub-kategori</p>
                @error('parent_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image/Icon --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Icon/Gambar Kategori
                </label>
                
                {{-- Tabs for upload type --}}
                <div class="flex gap-2 mb-4">
                    <button type="button" id="tabFile" class="px-4 py-2 rounded-lg text-sm font-bold transition" style="background:#72bf77;color:white">
                        📤 Upload Gambar
                    </button>
                    <button type="button" id="tabText" class="px-4 py-2 rounded-lg text-sm font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                        ✏️ Emoji/Teks
                    </button>
                </div>

                {{-- File Upload Tab --}}
                <div id="fileTab" class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition" id="dropZone">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <p class="text-sm font-bold text-gray-700">Drag gambar ke sini atau klik untuk browse</p>
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max: 2MB)</p>
                        <input type="file" 
                               id="image" 
                               name="image"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               class="hidden"
                               @error('image') aria-invalid="true" @enderror>
                    </div>
                    
                    {{-- Image Preview --}}
                    <div id="imagePreview" class="hidden">
                        <img id="previewImage" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg mx-auto mb-3">
                        <button type="button" id="removeImage" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-bold hover:bg-red-100 transition">
                            ❌ Hapus Gambar
                        </button>
                    </div>

                    @error('image')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Text Input Tab --}}
                <div id="textTab" class="hidden">
                    <input type="text" 
                           id="image_text" 
                           name="image_text" 
                           value="{{ old('image_text') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('image_text') border-red-300 @enderror"
                           placeholder="Contoh: 🍔">
                    <p class="mt-1 text-xs text-gray-500">Masukkan emoji atau teks untuk icon kategori</p>
                    @error('image_text')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-bold text-gray-700 mb-2">
                    Urutan Tampil
                </label>
                <input type="number" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', 0) }}"
                       min="0"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition @error('sort_order') border-red-300 @enderror"
                       placeholder="0">
                <p class="mt-1 text-xs text-gray-500">Angka lebih kecil akan tampil lebih dulu</p>
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Featured --}}
            <div class="flex items-start gap-3">
                <input type="checkbox" 
                       id="is_featured" 
                       name="is_featured" 
                       value="1"
                       {{ old('is_featured') ? 'checked' : '' }}
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <div>
                    <label for="is_featured" class="block text-sm font-bold text-gray-700">
                        Tampilkan di Featured
                    </label>
                    <p class="text-xs text-gray-500 mt-0.5">Kategori ini akan ditampilkan di halaman utama</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
                        style="background:#72bf77">
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching for file/text input
const tabFile = document.getElementById('tabFile');
const tabText = document.getElementById('tabText');
const fileTab = document.getElementById('fileTab');
const textTab = document.getElementById('textTab');

tabFile.addEventListener('click', () => {
    fileTab.classList.remove('hidden');
    textTab.classList.add('hidden');
    tabFile.style.background = '#72bf77';
    tabFile.style.color = 'white';
    tabText.style.background = '#f3f4f6';
    tabText.style.color = '#4b5563';
});

tabText.addEventListener('click', () => {
    fileTab.classList.add('hidden');
    textTab.classList.remove('hidden');
    tabText.style.background = '#72bf77';
    tabText.style.color = 'white';
    tabFile.style.background = '#f3f4f6';
    tabFile.style.color = '#4b5563';
});

// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');
const previewImage = document.getElementById('previewImage');
const removeImage = document.getElementById('removeImage');

// Click to browse
dropZone.addEventListener('click', () => fileInput.click());

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-green-400', 'bg-green-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-green-400', 'bg-green-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-green-400', 'bg-green-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
    }
});

// File input change
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

// Handle file selection and preview
function handleFileSelect(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        imagePreview.classList.remove('hidden');
        dropZone.classList.add('hidden');
    };
    reader.readAsDataURL(file);
}

// Remove image
removeImage.addEventListener('click', (e) => {
    e.preventDefault();
    fileInput.value = '';
    imagePreview.classList.add('hidden');
    dropZone.classList.remove('hidden');
});

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.autoGenerated) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        slugInput.value = slug;
        slugInput.dataset.autoGenerated = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    delete this.dataset.autoGenerated;
});
</script>
@endsection
