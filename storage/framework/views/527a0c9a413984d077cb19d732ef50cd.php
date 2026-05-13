<?php $__env->startSection('title', 'Settings - Arradea Seller'); ?>
<?php $__env->startSection('page_title', 'Pengaturan Toko'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-600 to-slate-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">Konfigurasi</p>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Pengaturan <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Toko</span>.</h1>
            <p class="text-gray-200 font-medium text-lg">Kelola informasi dan preferensi toko Anda.</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 p-5 lg:p-10">
        <h2 class="text-2xl font-black text-gray-900 mb-8">Informasi Toko</h2>

        <?php if(session('success')): ?>
        <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl">
            <span class="text-lg">✅</span>
            <p class="text-sm font-bold text-green-700"><?php echo e(session('success')); ?></p>
        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <span class="text-lg">❌</span>
            <ul class="text-sm font-medium text-red-700 space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('seller.settings.update')); ?>" class="space-y-8">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Nama Toko <span class="text-red-400">*</span></label>
                    <input type="text"
                           name="store_name"
                           value="<?php echo e(old('store_name', auth()->user()->store->name ?? '')); ?>"
                           required
                           placeholder="Nama toko Anda"
                           class="w-full px-6 py-4 bg-gray-50 border <?php echo e($errors->has('store_name') ? 'border-red-300' : 'border-gray-200'); ?> rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                    <?php $__errorArgs = ['store_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Alamat Toko</label>
                    <input type="text"
                           name="store_address"
                           value="<?php echo e(old('store_address', auth()->user()->store->address ?? '')); ?>"
                           placeholder="Alamat lengkap toko"
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-black text-gray-700 mb-2">Deskripsi Toko</label>
                    <textarea name="store_description"
                              rows="4"
                              placeholder="Ceritakan tentang toko Anda..."
                              class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium resize-none"><?php echo e(old('store_description', auth()->user()->store->description ?? '')); ?></textarea>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex justify-end">
                <button type="submit"
                        class="px-8 py-4 text-white font-black rounded-2xl transition hover:opacity-90 active:scale-95"
                        style="background:#72bf77">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        
            <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-4 lg:px-5 py-3 lg:py-4 border-b border-gray-50">
                    <h2 class="text-xs lg:text-sm font-black text-gray-700 uppercase tracking-widest">⚙️ Pengaturan Mode</h2>
                    <p class="text-[10px] lg:text-xs text-gray-500 mt-1">Pilih mode untuk mengakses fitur yang berbeda</p>
                </div>
                <div class="p-4 lg:p-5">
                    <?php if (isset($component)) { $__componentOriginal04361ff47368bd2d8243e28533d44e1a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal04361ff47368bd2d8243e28533d44e1a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bottom-sheet-switcher','data' => ['user' => Auth::user()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bottom-sheet-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Auth::user())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal04361ff47368bd2d8243e28533d44e1a)): ?>
<?php $attributes = $__attributesOriginal04361ff47368bd2d8243e28533d44e1a; ?>
<?php unset($__attributesOriginal04361ff47368bd2d8243e28533d44e1a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal04361ff47368bd2d8243e28533d44e1a)): ?>
<?php $component = $__componentOriginal04361ff47368bd2d8243e28533d44e1a; ?>
<?php unset($__componentOriginal04361ff47368bd2d8243e28533d44e1a); ?>
<?php endif; ?>
                </div>
            </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/seller/settings.blade.php ENDPATH**/ ?>