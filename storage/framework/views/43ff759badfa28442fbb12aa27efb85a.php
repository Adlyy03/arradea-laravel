<?php $__env->startSection('title', 'Edit Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('admin.products.index')); ?>" 
           class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Edit Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi produk <?php echo e($product->name); ?></p>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100 p-5">
        <div class="flex items-start gap-4">
            <img src="<?php echo e($product->image); ?>" 
                 alt="<?php echo e($product->name); ?>"
                 class="w-20 h-20 rounded-xl object-cover shadow-md"
                 onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
            <div class="flex-1">
                <h3 class="text-lg font-black text-gray-900"><?php echo e($product->name); ?></h3>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                    <span class="flex items-center gap-1 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <strong>Toko:</strong> <?php echo e($product->store->name ?? 'N/A'); ?>

                    </span>
                    <?php if($product->store && $product->store->user): ?>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-600">
                        <strong>Pemilik:</strong> <?php echo e($product->store->user->name); ?>

                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="<?php echo e(route('admin.products.update', $product)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?php echo e(old('name', $product->name)); ?>"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="<?php echo e(old('price', $product->price)); ?>"
                               min="0"
                               step="0.01"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required>
                    </div>
                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="discount_percent" class="block text-sm font-bold text-gray-700 mb-2">
                        Diskon (%)
                    </label>
                    <input type="number" 
                           id="discount_percent" 
                           name="discount_percent" 
                           value="<?php echo e(old('discount_percent', $product->discount_percent)); ?>"
                           min="0"
                           max="100"
                           step="0.01"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <div>
                    <label for="stock" class="block text-sm font-bold text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           value="<?php echo e(old('stock', $product->stock)); ?>"
                           min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           required>
                    <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2">
                        Kategori
                    </label>
                    <select id="category_id" 
                            name="category_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">— Pilih Kategori —</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="Deskripsi produk..."><?php echo e(old('description', $product->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-bold">Catatan Admin</p>
                    <p class="mt-1">Perubahan akan langsung terlihat oleh pembeli. Pastikan informasi yang dimasukkan sudah benar.</p>
                </div>
            </div>

            
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
                        style="background:#72bf77">
                    Simpan Perubahan
                </button>
                <a href="<?php echo e(route('admin.products.index')); ?>"
                   class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/admin/products/edit.blade.php ENDPATH**/ ?>