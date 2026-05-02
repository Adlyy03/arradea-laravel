@extends('layouts.dashboard')
@section('title', 'Kelola Kategori Produk')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Kategori Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori untuk mengorganisir produk di marketplace</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
           style="background:#72bf77">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            @foreach($errors->all() as $error)
                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Categories Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Parent</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Produk</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Toko</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-black text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold" style="background:rgba(114,191,119,.12);color:#72bf77">
                                    {{ $category->image ?: substr($category->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($category->parent)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">
                                    {{ $category->parent->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-bold text-gray-900">{{ $category->products_count }}</span>
                            <span class="text-xs text-gray-400">produk</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($category->stores_data->count() > 0)
                                <div class="space-y-1">
                                    @foreach($category->stores_data->take(3) as $store)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-gray-700">🏪 {{ $store['store_name'] }}</span>
                                            <span class="text-xs text-gray-400">({{ $store['product_count'] }})</span>
                                        </div>
                                    @endforeach
                                    @if($category->stores_data->count() > 3)
                                        <p class="text-xs text-gray-400">+{{ $category->stores_data->count() - 3 }} toko lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Belum ada toko</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($category->is_featured)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-white" style="background:#72bf77">
                                    ⭐ Featured
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-blue-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button type="button"
                                        onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->products_count }})"
                                        class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl" style="background:rgba(114,191,119,.12)">
                                    🏷️
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Belum Ada Kategori</p>
                                    <p class="text-xs text-gray-400 mt-1">Tambahkan kategori pertama untuk mengorganisir produk</p>
                                </div>
                                <a href="{{ route('admin.categories.create') }}" 
                                   class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90"
                                   style="background:#72bf77">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Kategori
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(114,191,119,.12)">
                    🏷️
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ $categories->count() }}</p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Kategori</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(245,158,11,.12)">
                    ⭐
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ $categories->where('is_featured', true)->count() }}</p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Featured</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(59,130,246,.12)">
                    📦
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ $categories->sum('products_count') }}</p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Produk</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeDeleteModal()"></div>

        {{-- Center modal --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-6 pb-4 bg-white sm:p-8">
                {{-- Icon --}}
                <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-2xl" style="background:rgba(239,68,68,.12)">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-5 text-center">
                    <h3 class="text-2xl font-black text-gray-900" id="modal-title">
                        Hapus Kategori?
                    </h3>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm text-gray-600">
                            Anda yakin ingin menghapus kategori:
                        </p>
                        <div class="p-4 rounded-xl bg-gray-50">
                            <p class="text-base font-bold text-gray-900" id="categoryName"></p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span id="productCount"></span>
                            </p>
                        </div>
                        <p class="text-sm text-red-600 font-medium mt-3">
                            ⚠️ Tindakan ini tidak dapat dibatalkan!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-gray-50 sm:px-8 sm:flex sm:flex-row-reverse gap-3">
                <form id="deleteForm" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-red-600 rounded-xl hover:bg-red-700 active:scale-95 shadow-lg hover:shadow-xl">
                        Ya, Hapus Kategori
                    </button>
                </form>
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="w-full sm:w-auto px-6 py-3 mt-3 sm:mt-0 text-sm font-bold text-gray-700 transition-all duration-200 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 active:scale-95">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(categoryId, categoryName, productCount) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const categoryNameEl = document.getElementById('categoryName');
    const productCountEl = document.getElementById('productCount');
    
    // Set form action
    form.action = `/admin/categories/${categoryId}`;
    
    // Set category info
    categoryNameEl.textContent = categoryName;
    productCountEl.textContent = productCount > 0 
        ? `Memiliki ${productCount} produk` 
        : 'Tidak ada produk';
    
    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.inline-block').classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const panel = modal.querySelector('.inline-block');
    
    // Hide with animation
    panel.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<style>
#deleteModal .inline-block {
    transform: scale(0.95);
    opacity: 0;
    transition: all 0.2s ease-out;
}

#deleteModal .inline-block.scale-100 {
    transform: scale(1);
}

#deleteModal .inline-block.opacity-100 {
    opacity: 1;
}
</style>
@endsection
