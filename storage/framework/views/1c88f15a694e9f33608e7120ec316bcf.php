<?php $__env->startSection('title', 'Keluhan User'); ?>
<?php $__env->startSection('page_title', 'Keluhan User'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-gray-900">Keluhan Hari Ini</h2>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.complaints.export', ['days' => 1])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 1 Hari
            </a>
            <a href="<?php echo e(route('admin.complaints.export', ['days' => 7])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 7 Hari
            </a>
            <a href="<?php echo e(route('admin.complaints.export', ['days' => 30])); ?>" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 30 Hari
            </a>
        </div>
    </div>

    
    <?php $__empty_1 = true; $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $complaint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-400"><?php echo e($complaint->created_at->format('d M Y, H:i')); ?></span>
                </div>
                <p class="text-sm font-bold text-gray-900 mb-1">
                    Dari: <?php echo e($complaint->user->name); ?> 
                    <span class="text-xs font-normal text-gray-400">(<?php echo e($complaint->user->is_seller ? 'Seller' : 'Buyer'); ?>)</span>
                </p>
                <p class="text-xs text-gray-500 mb-2"><?php echo e($complaint->subject); ?></p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-3">
            <p class="text-sm text-gray-700"><?php echo e($complaint->message); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
        <p class="text-gray-400">Belum ada keluhan hari ini</p>
    </div>
    <?php endif; ?>

    <?php if($complaints->hasPages()): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <?php echo e($complaints->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/admin/complaints/index.blade.php ENDPATH**/ ?>