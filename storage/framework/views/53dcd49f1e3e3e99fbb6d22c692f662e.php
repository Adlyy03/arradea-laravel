<?php $__env->startSection('title', 'Kategori Produk - Arradea'); ?>
<?php $__env->startSection('page_title', 'Semua Kategori'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-6 sm:px-8 py-8 lg:py-16 lg:py-16 lg:py-32">
    <!-- Header -->
    <div class="text-center mb-12 lg:mb-20">
        <h1 class="text-4xl lg:text-6xl font-black tracking-tighter mb-4">Kategori <span class="text-primary-600">Produk</span>.</h1>
        <p class="text-gray-400 text-lg lg:text-xl font-medium max-w-2xl mx-auto">Jelajahi berbagai kategori produk dari penjual terpercaya di sekitar Anda.</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-6 lg:gap-12">
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="group bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-6 lg:p-12 shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden">
                <div class="relative">
                    <?php if($category->image && (strpos($category->image, '/') !== false || strpos($category->image, 'http') !== false)): ?>
                        <img src="<?php echo e($category->image); ?>" alt="<?php echo e($category->name); ?>" class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl lg:rounded-3xl object-cover mb-6 lg:mb-8 group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-primary-50 rounded-2xl lg:rounded-3xl flex items-center justify-center mb-6 lg:mb-8 group-hover:bg-primary-100 transition-colors">
                            <span class="text-3xl lg:text-4xl font-black text-primary-600"><?php echo e($category->image ?: substr($category->name, 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>
                    <h3 class="text-2xl lg:text-3xl font-black text-gray-900 mb-4"><?php echo e($category->name); ?></h3>
                    <p class="text-gray-500 font-medium mb-6 lg:mb-8 leading-relaxed"><?php echo e($category->description); ?></p>

                    <?php if($category->children->count() > 0): ?>
                        <div class="mb-6 lg:mb-8">
                            <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-3">Sub Kategori:</p>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $category->children->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('categories.show', $child)); ?>" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-primary-100 hover:text-primary-700 transition-colors">
                                        <?php echo e($child->name); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($category->children->count() > 3): ?>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm font-medium">
                                        +<?php echo e($category->children->count() - 3); ?> lagi
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400 font-medium">
                            <?php echo e($category->getProductsCount()); ?> produk tersedia
                        </div>
                        <a href="<?php echo e(route('categories.show', $category)); ?>" class="px-6 py-3 bg-primary-600 text-white rounded-2xl font-black text-sm hover:bg-primary-700 transition-colors">
                            Jelajahi →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-10 lg:py-20">
                <div class="text-4xl lg:text-6xl mb-4">📂</div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Kategori</h3>
                <p class="text-gray-500 font-medium">Kategori produk akan segera ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/categories/index.blade.php ENDPATH**/ ?>