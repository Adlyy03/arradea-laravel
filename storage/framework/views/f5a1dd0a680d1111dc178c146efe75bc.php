<?php $__env->startSection('title', 'Kelola Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Kelola Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua produk dari seluruh toko di marketplace</p>
        </div>
    </div>

    
    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
    </div>
    <?php endif; ?>

    
    <?php if($errors->any()): ?>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(114,191,119,.12)">
                    📦
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900"><?php echo e(number_format($totalProducts)); ?></p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Produk</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(239,68,68,.12)">
                    ⚠️
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900"><?php echo e(number_format($outOfStock)); ?></p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Stok Habis</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(245,158,11,.12)">
                    📉
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900"><?php echo e(number_format($lowStock)); ?></p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Stok Rendah</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background:rgba(59,130,246,.12)">
                    💰
                </div>
                <div>
                    <p class="text-lg font-black text-gray-900">Rp <?php echo e(number_format($totalValue, 0, ',', '.')); ?></p>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Nilai Inventori</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Cari Produk</label>
                <input type="text" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Nama produk..."
                       class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Toko</label>
                <select name="store_id" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    <option value="">Semua Toko</option>
                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($store->id); ?>" <?php echo e(request('store_id') == $store->id ? 'selected' : ''); ?>>
                            <?php echo e($store->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Status Stok</label>
                <select name="stock_status" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    <option value="">Semua Status</option>
                    <option value="available" <?php echo e(request('stock_status') == 'available' ? 'selected' : ''); ?>>Tersedia (>10)</option>
                    <option value="low" <?php echo e(request('stock_status') == 'low' ? 'selected' : ''); ?>>Stok Rendah (1-10)</option>
                    <option value="out" <?php echo e(request('stock_status') == 'out' ? 'selected' : ''); ?>>Habis (0)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Produk</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Toko</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="px-5 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-wider">Stok</th>
                        <th class="px-5 py-4 text-right text-xs font-black text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e($product->image); ?>" 
                                     alt="<?php echo e($product->name); ?>"
                                     class="w-12 h-12 rounded-xl object-cover"
                                     onerror="this.src='https://via.placeholder.com/100x100/f0faf1/72bf77?text=P'">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 line-clamp-1"><?php echo e($product->name); ?></p>
                                    <?php if($product->discount_percent > 0): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold text-white mt-1" style="background:#72bf77">
                                            -<?php echo e($product->discount_percent); ?>% OFF
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">🏪 <?php echo e($product->store->name ?? 'N/A'); ?></span>
                            </div>
                            <?php if($product->store && $product->store->user): ?>
                                <p class="text-xs text-gray-400 mt-0.5"><?php echo e($product->store->user->name); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($product->category): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">
                                    <?php echo e($product->category->name); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($product->discount_percent > 0): ?>
                                <?php $finalPrice = $product->price * (1 - $product->discount_percent/100); ?>
                                <div>
                                    <p class="text-xs text-gray-400 line-through">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                                    <p class="text-sm font-bold" style="color:#72bf77">Rp <?php echo e(number_format($finalPrice, 0, ',', '.')); ?></p>
                                </div>
                            <?php else: ?>
                                <p class="text-sm font-bold text-gray-900">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($product->stock == 0): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">
                                    Habis
                                </span>
                            <?php elseif($product->stock <= 10): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">
                                    <?php echo e($product->stock); ?> unit
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                    <?php echo e($product->stock); ?> unit
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo e(route('admin.products.edit', $product)); ?>"
                                   class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-blue-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button type="button"
                                        onclick="openDeleteModal(<?php echo e($product->id); ?>, '<?php echo e(addslashes($product->name)); ?>', '<?php echo e(addslashes($product->store->name ?? 'N/A')); ?>')"
                                        class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl" style="background:rgba(114,191,119,.12)">
                                    📦
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Tidak Ada Produk</p>
                                    <p class="text-xs text-gray-400 mt-1">Belum ada produk yang sesuai dengan filter</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if($products->hasPages()): ?>
        <div class="px-5 py-4 border-t border-gray-100">
            <?php echo e($products->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeDeleteModal()"></div>

        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 pt-6 pb-4 bg-white sm:p-8">
                
                <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-2xl" style="background:rgba(239,68,68,.12)">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                
                <div class="mt-5 text-center">
                    <h3 class="text-2xl font-black text-gray-900" id="modal-title">
                        Hapus Produk?
                    </h3>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm text-gray-600">
                            Anda yakin ingin menghapus produk:
                        </p>
                        <div class="p-4 rounded-xl bg-gray-50">
                            <p class="text-base font-bold text-gray-900" id="productName"></p>
                            <p class="text-sm text-gray-500 mt-1">
                                dari toko <span class="font-semibold" id="storeName"></span>
                            </p>
                        </div>
                        <p class="text-sm text-red-600 font-medium mt-3">
                            ⚠️ Tindakan ini tidak dapat dibatalkan!
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="px-6 py-4 bg-gray-50 sm:px-8 sm:flex sm:flex-row-reverse gap-3">
                <form id="deleteForm" method="POST" class="flex-1 sm:flex-none">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-red-600 rounded-xl hover:bg-red-700 active:scale-95 shadow-lg hover:shadow-xl">
                        Ya, Hapus Produk
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
function openDeleteModal(productId, productName, storeName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const productNameEl = document.getElementById('productName');
    const storeNameEl = document.getElementById('storeName');
    
    // Set form action
    form.action = `/admin/products/${productId}`;
    
    // Set product info
    productNameEl.textContent = productName;
    storeNameEl.textContent = storeName;
    
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/admin/products/index.blade.php ENDPATH**/ ?>