<?php $isEdit = isset($product); ?>
<?php $__env->startSection('title', ($isEdit ? 'Edit Produk' : 'Tambah Produk') . ' - Arradea'); ?>
<?php $__env->startSection('page_title', $isEdit ? 'Edit Produk' : 'Produk Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-5 fade-up">

    
    <a href="/seller/products" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-900 transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Produk
    </a>

    
    <?php if($errors->any()): ?>
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <span class="text-lg flex-shrink-0">❌</span>
        <div>
            <p class="text-sm font-black text-red-700 mb-1">Ada kesalahan input:</p>
            <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full opacity-10" style="background:#72bf77;filter:blur(50px)"></div>
        <div class="absolute -bottom-16 -left-8 w-40 h-40 rounded-full opacity-10" style="background:#4db85a;filter:blur(35px)"></div>
        <div class="relative z-10 text-white">
            <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">
                <?php echo e($isEdit ? 'Edit Katalog' : 'Tambah Katalog'); ?>

            </p>
            <h1 class="text-2xl lg:text-3xl font-black tracking-tight">
                <?php echo e($isEdit ? 'Perbarui' : 'Input'); ?> <span style="color:#a3e4a6">Produk</span><?php echo e($isEdit ? '' : ' Baru'); ?>

            </h1>
            <p class="text-white/50 text-sm mt-1.5">Berikan foto berkualitas tinggi dan deskripsi menarik.</p>
        </div>
    </div>

    
    <form action="<?php echo e($isEdit ? '/web/product/'.$product->id.'/update' : '/web/product/store'); ?>"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        <?php echo csrf_field(); ?>
        <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest mb-4">📸 Foto Produk</h2>

            <div class="flex flex-col sm:flex-row gap-5 items-start">
                
                <div id="imagePreviewWrap"
                     class="w-full sm:w-36 h-36 rounded-2xl overflow-hidden flex-shrink-0 border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center cursor-pointer hover:border-green-400 transition group"
                     onclick="document.getElementById('imageInput').click()">
                    <img id="imagePreview"
                         src="<?php echo e($isEdit && $product->image ? $product->image : ''); ?>"
                         alt="preview"
                         class="<?php echo e(($isEdit && $product->image) ? '' : 'hidden'); ?> w-full h-full object-cover">
                    <div id="imagePreviewPlaceholder" class="<?php echo e(($isEdit && $product->image) ? 'hidden' : ''); ?> text-center p-3">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-1 group-hover:text-green-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-[10px] font-bold text-gray-400">Klik untuk upload</p>
                    </div>
                </div>

                <div class="flex-1 space-y-2">
                    <input type="file" name="image" accept="image/*" id="imageInput" class="hidden">
                    <button type="button" onclick="document.getElementById('imageInput').click()"
                            class="w-full h-10 border-2 border-dashed border-gray-300 rounded-xl text-sm font-bold text-gray-500 hover:border-green-400 hover:text-green-600 transition">
                        <?php echo e($isEdit && $product->image ? '🔄 Ganti Foto' : '📂 Pilih Foto'); ?>

                    </button>
                    <p class="text-[10px] text-gray-400">Format: JPG, PNG, WEBP · Maks 2MB · Rasio 1:1 direkomendasikan</p>
                    <?php if($isEdit && $product->image): ?>
                        <p class="text-[10px] text-green-600 font-bold">✅ Foto tersimpan. Kosongkan jika tidak ingin mengubah.</p>
                    <?php endif; ?>
                    <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">📋 Informasi Dasar</h2>

            
            <div class="space-y-1.5">
                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Nama Produk <span class="text-red-400">*</span></label>
                <input type="text" name="name"
                       value="<?php echo e(old('name', $isEdit ? $product->name : '')); ?>"
                       required
                       placeholder="contoh: Kemeja Batik Premium"
                       class="w-full h-11 bg-gray-50 border <?php echo e($errors->has('name') ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'); ?> rounded-xl px-4 text-sm font-semibold text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition"
                       style="--tw-ring-color:rgba(114,191,119,.4)">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="space-y-1.5">
                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Kategori</label>
                <select name="category_id"
                        class="w-full h-11 bg-gray-50 border <?php echo e($errors->has('category_id') ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'); ?> rounded-xl px-4 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition appearance-none"
                        style="--tw-ring-color:rgba(114,191,119,.4)">
                    <option value="">— Pilih Kategori (Opsional) —</option>
                    <?php $__currentLoopData = \App\Models\Category::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>"
                            <?php echo e(old('category_id', $isEdit ? $product->category_id : '') == $cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="space-y-1.5">
                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                          placeholder="Jelaskan produk Anda secara detail: bahan, ukuran, keunggulan, dll."
                          class="w-full bg-gray-50 border <?php echo e($errors->has('description') ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'); ?> rounded-xl px-4 py-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition resize-none"
                          style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('description', $isEdit ? $product->description : '')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Harga (Rp) <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">Rp</span>
                        <input type="number" name="price"
                               value="<?php echo e(old('price', $isEdit ? $product->price : '')); ?>"
                               required min="0"
                               placeholder="150000"
                               class="w-full h-11 bg-gray-50 border <?php echo e($errors->has('price') ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'); ?> rounded-xl pl-9 pr-4 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition"
                               style="--tw-ring-color:rgba(114,191,119,.4)">
                    </div>
                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Stok <span class="text-red-400">*</span></label>
                    <input type="number" name="stock"
                           value="<?php echo e(old('stock', $isEdit ? $product->stock : '')); ?>"
                           required min="0"
                           placeholder="10"
                           class="w-full h-11 bg-gray-50 border <?php echo e($errors->has('stock') ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'); ?> rounded-xl px-4 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="--tw-ring-color:rgba(114,191,119,.4)">
                    <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">🏷️ Pengaturan Diskon</h2>
                <span class="text-[10px] text-gray-400 font-bold">Opsional</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Diskon (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="discount_percent"
                               value="<?php echo e(old('discount_percent', $isEdit ? $product->discount_percent : 0)); ?>"
                               placeholder="0"
                               id="discountPercent"
                               class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 pr-8 text-sm font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition"
                               style="--tw-ring-color:rgba(114,191,119,.4)">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">%</span>
                    </div>
                    <?php $__errorArgs = ['discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Diskon Dari</label>
                    <input type="datetime-local" name="discount_start_at"
                           value="<?php echo e(old('discount_start_at', $isEdit && $product->discount_start_at ? $product->discount_start_at->format('Y-m-d\TH:i') : '')); ?>"
                           class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="--tw-ring-color:rgba(114,191,119,.4)">
                    <?php $__errorArgs = ['discount_start_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider">Diskon Sampai</label>
                    <input type="datetime-local" name="discount_end_at"
                           value="<?php echo e(old('discount_end_at', $isEdit && $product->discount_end_at ? $product->discount_end_at->format('Y-m-d\TH:i') : '')); ?>"
                           class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="--tw-ring-color:rgba(114,191,119,.4)">
                    <?php $__errorArgs = ['discount_end_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div id="discountPreview" class="hidden p-3 bg-green-50 border border-green-200 rounded-xl">
                <p class="text-xs font-bold text-green-700">💡 Preview: Harga setelah diskon = <span id="discountedPrice" class="font-black">—</span></p>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">🎛️ Varian Produk</h2>
                <span class="text-[10px] text-gray-400 font-bold">Opsional — jika produk punya pilihan</span>
            </div>

            <div id="variantsList" class="space-y-3"></div>

            <button type="button" onclick="addVariantField()"
                    class="w-full h-10 border-2 border-dashed border-gray-300 rounded-xl text-sm font-bold text-gray-500 hover:border-green-400 hover:text-green-600 hover:bg-green-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Varian
            </button>

            <textarea name="variants_json" id="variantsJSON" class="hidden"><?php echo e(old('variants_json', $isEdit ? json_encode($product->variants ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '[]')); ?></textarea>
            <?php $__errorArgs = ['variants_json'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 font-bold"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="flex flex-col sm:flex-row gap-3 pb-6">
            <button type="submit"
                    class="flex-1 h-12 rounded-2xl font-black text-sm text-white transition hover:opacity-90 active:scale-95 flex items-center justify-center gap-2 shadow-lg"
                    style="background:#72bf77;box-shadow:0 8px 24px rgba(114,191,119,.3)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <?php echo e($isEdit ? 'Simpan Perubahan' : 'Publish Produk'); ?>

            </button>
            <a href="/seller/products"
               class="flex-1 sm:flex-none sm:w-32 h-12 rounded-2xl font-black text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
// ── Image Preview ──
document.getElementById('imageInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('imagePreview');
            const ph = document.getElementById('imagePreviewPlaceholder');
            img.src = event.target.result;
            img.classList.remove('hidden');
            if (ph) ph.classList.add('hidden');
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

// ── Discount Preview ──
function updateDiscountPreview() {
    const priceInput = document.querySelector('input[name="price"]');
    const discountInput = document.getElementById('discountPercent');
    const preview = document.getElementById('discountPreview');
    const discountedEl = document.getElementById('discountedPrice');
    if (!priceInput || !discountInput || !preview) return;
    const price = Number(priceInput.value) || 0;
    const discount = Number(discountInput.value) || 0;
    if (price > 0 && discount > 0) {
        const final = price - (price * discount / 100);
        discountedEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(final));
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}
document.querySelector('input[name="price"]')?.addEventListener('input', updateDiscountPreview);
document.getElementById('discountPercent')?.addEventListener('input', updateDiscountPreview);
updateDiscountPreview();

// ── Variant Builder ──
let variantCounter = 0;

function generateUniqueKey(name) {
    return name.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]/g, '').substring(0, 20);
}

function addVariantField(data = null) {
    const variantsList = document.getElementById('variantsList');
    const variantId = variantCounter++;
    const num = variantsList.children.length + 1;

    const html = `
        <div class="variant-item bg-gray-50 border border-gray-200 p-4 rounded-2xl space-y-3 transition-all" data-variant-id="${variantId}" style="opacity:0;transform:translateY(8px)">
            <div class="flex justify-between items-center">
                <span class="text-xs font-black text-gray-600 uppercase tracking-widest">Varian ${num}</span>
                <button type="button" onclick="removeVariantField(${variantId})" class="px-2.5 py-1 bg-red-100 text-red-600 rounded-lg text-[10px] font-black hover:bg-red-200 transition">✕ Hapus</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" placeholder="Nama varian (Warna Merah, Size M...)" class="variant-name col-span-2 h-10 bg-white border border-gray-200 rounded-xl px-3 text-sm font-semibold focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.name || ''}">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">Rp</span>
                    <input type="number" placeholder="Harga" class="variant-price w-full h-10 bg-white border border-gray-200 rounded-xl pl-8 pr-3 text-sm font-semibold focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.price || ''}">
                </div>
                <input type="number" placeholder="Stok (opsional)" class="variant-stock h-10 bg-white border border-gray-200 rounded-xl px-3 text-sm font-semibold focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.stock || ''}">
            </div>
            <details class="cursor-pointer">
                <summary class="text-[10px] font-black text-gray-400 hover:text-green-600 uppercase tracking-widest transition select-none">⚙️ Pengaturan Diskon Varian</summary>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-gray-200">
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" placeholder="Diskon %" class="variant-discount w-full h-10 bg-white border border-gray-200 rounded-xl px-3 pr-7 text-sm font-semibold focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.discount_percent || ''}">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">%</span>
                    </div>
                    <input type="datetime-local" class="variant-discount-start h-10 bg-white border border-gray-200 rounded-xl px-3 text-xs font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.discount_start_at ? data.discount_start_at.replace(' ', 'T') : ''}">
                    <input type="datetime-local" class="variant-discount-end h-10 bg-white border border-gray-200 rounded-xl px-3 text-xs font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)" value="${data?.discount_end_at ? data.discount_end_at.replace(' ', 'T') : ''}">
                </div>
            </details>
        </div>`;

    variantsList.insertAdjacentHTML('beforeend', html);
    const el = variantsList.lastElementChild;
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });
    updateVariantsJSON();
}

function removeVariantField(variantId) {
    const item = document.querySelector(`[data-variant-id="${variantId}"]`);
    if (item) {
        item.style.opacity = '0';
        item.style.transform = 'scale(0.95)';
        setTimeout(() => { item.remove(); updateVariantsJSON(); }, 200);
    }
}

function updateVariantsJSON() {
    const variants = [];
    document.querySelectorAll('.variant-item').forEach(item => {
        const name = item.querySelector('.variant-name').value.trim();
        const price = item.querySelector('.variant-price').value.trim();
        if (name && price) {
            const variant = {
                key: generateUniqueKey(name),
                name,
                price: parseInt(price) || 0,
                stock: parseInt(item.querySelector('.variant-stock').value) || 0,
            };
            const discount = item.querySelector('.variant-discount').value.trim();
            if (discount) variant.discount_percent = parseFloat(discount) || 0;
            const start = item.querySelector('.variant-discount-start').value.trim();
            if (start) variant.discount_start_at = start.replace('T', ' ') + ':00';
            const end = item.querySelector('.variant-discount-end').value.trim();
            if (end) variant.discount_end_at = end.replace('T', ' ') + ':00';
            variants.push(variant);
        }
    });
    document.getElementById('variantsJSON').value = JSON.stringify(variants);
}

document.addEventListener('input', function(e) {
    const classes = ['variant-name','variant-price','variant-stock','variant-discount','variant-discount-start','variant-discount-end'];
    if (classes.some(c => e.target.classList.contains(c))) updateVariantsJSON();
});

window.addEventListener('DOMContentLoaded', function() {
    const existingJSON = document.getElementById('variantsJSON').value.trim();
    if (existingJSON && existingJSON !== '[]') {
        try {
            const variants = JSON.parse(existingJSON);
            if (Array.isArray(variants)) variants.forEach(v => addVariantField(v));
        } catch(e) {}
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/seller/products/create.blade.php ENDPATH**/ ?>