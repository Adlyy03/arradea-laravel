<?php $__env->startSection('title', 'Manajemen Produk - Arradea'); ?>
<?php $__env->startSection('page_title', 'Produk Toko'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5 fade-up">

    
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-32 opacity-5" style="background:#72bf77;filter:blur(80px)"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Inventori Toko</p>
                <h1 class="text-2xl lg:text-4xl font-black tracking-tight leading-tight">
                    Kelola <span style="color:#a3e4a6">Produk</span> Anda
                </h1>
                <p class="text-white/50 text-sm mt-1.5">Tambah, edit, dan pantau stok produk toko Anda secara real-time.</p>
            </div>
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <a href="<?php echo e(route('seller.products.create')); ?>"
                   class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90 active:scale-95"
                   style="background:#72bf77;transition:all 0.18s cubic-bezier(0.4,0,0.2,1)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Produk Baru
                </a>
            </div>
        </div>

        
        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6" style="border-top:1px solid rgba(255,255,255,.08)">
            <div class="text-center text-white">
                <p class="text-2xl font-black" id="stat-total"><?php echo e($products->count()); ?></p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Total Produk</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-red-400" id="low-stock-count"><?php echo e($products->where('stock', '<=', 5)->count()); ?></p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Stok Menipis</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-amber-400"><?php echo e($products->where('discount_percent', '>', 0)->count()); ?></p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Produk Diskon</p>
            </div>
            <div class="text-center text-white">
                <p class="text-2xl font-black text-green-400"><?php echo e($products->where('stock', '>', 0)->count()); ?></p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Stok Tersedia</p>
            </div>
        </div>
    </div>

    
    <?php if($products->where('stock', '<=', 5)->count() > 0): ?>
    <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-2xl" id="low-stock-alert">
        <div class="flex items-center gap-3">
            <span class="text-xl animate-pulse">⚠️</span>
            <div>
                <p class="text-sm font-black text-red-800"><?php echo e($products->where('stock', '<=', 5)->count()); ?> Produk dengan stok rendah</p>
                <p class="text-xs text-red-600">Segera lakukan restock agar pembeli tidak kecewa.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="productSearch" placeholder="Cari produk..." class="w-full h-10 bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 text-sm font-medium focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color:rgba(114,191,119,.4)">
        </div>
        <select id="stockFilter" class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
            <option value="all">Semua Stok</option>
            <option value="low">Stok Menipis (≤5)</option>
            <option value="ok">Stok Aman (>5)</option>
        </select>
        <select id="sortFilter" class="h-10 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
            <option value="default">Urutan Default</option>
            <option value="price-asc">Harga: Terendah</option>
            <option value="price-desc">Harga: Tertinggi</option>
            <option value="stock-asc">Stok: Terendah</option>
        </select>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">📦 Katalog Produk</h2>
            <span class="text-xs font-bold text-gray-400" id="product-count-label"><?php echo e($products->count()); ?> item</span>
        </div>

        
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left" id="products-table">
                <thead class="bg-gray-50/80 text-[10px] font-black tracking-widest uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-4">Detail Produk</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Harga</th>
                        <th class="px-5 py-4">Stok</th>
                        <th class="px-5 py-4">Diskon</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="products-tbody">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="product-row hover:bg-gray-50/60 transition-all duration-200"
                        data-product-id="<?php echo e($product->id); ?>"
                        data-name="<?php echo e(strtolower($product->name)); ?>"
                        data-stock="<?php echo e($product->stock); ?>"
                        data-price="<?php echo e($product->price); ?>">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm">
                                    <img src="<?php echo e($product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'); ?>"
                                         alt="<?php echo e($product->name); ?>"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-gray-900 leading-tight"><?php echo e($product->name); ?></p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5"><?php echo e(Str::limit($product->description ?? 'Tidak ada deskripsi', 60)); ?></p>
                                    <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-widest text-gray-400">ID-<?php echo e(str_pad($product->id, 5, '0', STR_PAD_LEFT)); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($product->category): ?>
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg text-[10px] font-black uppercase tracking-widest"><?php echo e($product->category->name); ?></span>
                            <?php else: ?>
                                <span class="text-xs text-gray-300 font-bold">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <p class="product-price font-black text-gray-900">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                            <?php if(($product->discount_percent ?? 0) > 0): ?>
                            <p class="text-[10px] text-green-600 font-bold mt-0.5">Diskon <?php echo e($product->discount_percent); ?>%</p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="product-stock-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase
                                    <?php echo e($product->stock > 5 ? 'bg-green-100 text-green-700' : ($product->stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')); ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?php echo e($product->stock > 5 ? 'bg-green-500' : ($product->stock > 0 ? 'bg-amber-500' : 'bg-red-500')); ?>"></span>
                                    <?php echo e($product->stock); ?> unit
                                </span>
                                <p class="product-status-note text-[9px] font-bold uppercase <?php echo e($product->stock <= 5 ? 'text-red-400 animate-pulse' : 'text-green-500'); ?>">
                                    <?php echo e($product->stock <= 5 ? ($product->stock == 0 ? 'Habis!' : 'Restock!') : 'Aman'); ?>

                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <?php if(($product->discount_percent ?? 0) > 0): ?>
                                <span class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-[10px] font-black"><?php echo e($product->discount_percent); ?>% OFF</span>
                            <?php else: ?>
                                <span class="text-xs text-gray-300 font-bold">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <form action="<?php echo e(url('/web/product/' . $product->id . '/toggle-active')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit"
                                            class="w-9 h-9 border rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95 <?php echo e($product->is_active ? 'bg-green-50 border-green-200 text-green-600 hover:bg-green-500 hover:text-white hover:border-transparent' : 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-500 hover:text-white hover:border-transparent'); ?>"
                                            style="transition:all 0.18s cubic-bezier(0.4,0,0.2,1); cursor: pointer !important; position: relative; z-index: 100 !important;"
                                            title="<?php echo e($product->is_active ? 'Klik untuk Nonaktifkan' : 'Klik untuk Aktifkan'); ?>">
                                        <?php if($product->is_active): ?>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <?php else: ?>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        <?php endif; ?>
                                    </button>
                                </form>
                                <a href="<?php echo e(route('seller.products.edit', $product->id)); ?>"
                                   class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-green-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                   style="transition:all 0.18s cubic-bezier(0.4,0,0.2,1); cursor: pointer; position: relative; z-index: 100;"
                                   title="Edit Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <button type="button"
                                        onclick="window.openDeleteModal(<?php echo e($product->id); ?>, <?php echo \Illuminate\Support\Js::from($product->name)->toHtml() ?>)"
                                        class="w-9 h-9 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:border-transparent hover:scale-110 active:scale-95 transition-all"
                                        style="transition:all 0.18s cubic-bezier(0.4,0,0.2,1); cursor: pointer !important; position: relative; z-index: 100 !important;"
                                        title="Hapus Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr id="empty-row">
                        <td colspan="6" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-5xl">📦</span>
                                <p class="font-black text-gray-900 text-lg">Belum Ada Produk</p>
                                <p class="text-sm text-gray-400">Mulailah dengan menambahkan produk pertama toko Anda.</p>
                                <a href="/seller/products/create"
                                   class="mt-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90"
                                   style="background:#72bf77">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="lg:hidden divide-y divide-gray-50" id="products-mobile">
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="product-row p-4 flex gap-4 hover:bg-gray-50/50 transition"
                 data-product-id="<?php echo e($product->id); ?>"
                 data-name="<?php echo e(strtolower($product->name)); ?>"
                 data-stock="<?php echo e($product->stock); ?>"
                 data-price="<?php echo e($product->price); ?>">
                <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                    <img src="<?php echo e($product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'); ?>"
                         alt="<?php echo e($product->name); ?>"
                         class="w-full h-full object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-black text-gray-900 leading-tight text-sm"><?php echo e($product->name); ?></p>
                        <span class="product-stock-badge flex-shrink-0 px-2 py-0.5 rounded-lg text-[9px] font-black <?php echo e($product->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>"><?php echo e($product->stock); ?>x</span>
                    </div>
                    <?php if($product->category): ?>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest"><?php echo e($product->category->name); ?></span>
                    <?php endif; ?>
                    <p class="product-price text-sm font-black text-gray-900 mt-1">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></p>
                    <div class="flex items-center gap-2 mt-2">
                        <form action="<?php echo e(url('/web/product/' . $product->id . '/toggle-active')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition <?php echo e($product->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'); ?>"
                                    style="cursor: pointer !important; position: relative; z-index: 100 !important;">
                                <?php echo e($product->is_active ? 'Aktif' : 'Nonaktif'); ?>

                            </button>
                        </form>
                        <a href="<?php echo e(route('seller.products.edit', $product->id)); ?>"
                           class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700 transition"
                           style="cursor: pointer; position: relative; z-index: 100;">Edit</a>
                        <button type="button"
                                onclick="window.openDeleteModal(<?php echo e($product->id); ?>, <?php echo \Illuminate\Support\Js::from($product->name)->toHtml() ?>)"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 transition"
                                style="cursor: pointer !important; position: relative; z-index: 100 !important;">Hapus</button>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <span class="text-4xl">📦</span>
                <p class="font-black text-gray-900 mt-3">Belum Ada Produk</p>
                <a href="<?php echo e(route('seller.products.create')); ?>" class="mt-3 inline-block px-5 py-2 rounded-xl text-sm font-bold text-white" style="background:#72bf77">Tambah</a>
            </div>
            <?php endif; ?>
        </div>

        
        <div id="no-search-results" class="hidden px-10 py-12 text-center">
            <span class="text-4xl">🔍</span>
            <p class="font-black text-gray-900 mt-2">Produk tidak ditemukan</p>
            <p class="text-sm text-gray-400 mt-1">Coba kata kunci yang berbeda.</p>
        </div>
    </div>
</div>


<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 space-y-5" style="animation:fadeInUp .2s ease">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <p class="font-black text-gray-900">Hapus Produk?</p>
                <p class="text-xs text-gray-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="p-3 bg-red-50 rounded-2xl border border-red-100">
            <p class="text-sm font-bold text-red-700" id="delete-product-name">—</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 h-11 rounded-2xl font-bold text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 transition">Batal</button>
            <form id="delete-form" method="POST" class="flex-1">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-full h-11 rounded-2xl font-black text-sm text-white bg-red-500 hover:bg-red-600 transition active:scale-95">Hapus Sekarang</button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function () {
    const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const broadcastKey = <?php echo json_encode(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')), 512) ?>;
    const broadcastCluster = <?php echo json_encode(env('PUSHER_APP_CLUSTER', 'mt1'), 512) ?>;
    const broadcastHost = <?php echo json_encode(env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1'))) ?>;
    const broadcastPort = Number(<?php echo json_encode((int) env('REVERB_PORT', env('PUSHER_PORT', 8080))) ?>);
    const broadcastScheme = <?php echo json_encode(env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http'))) ?>;

    // ── Search & Filter ──────────────────────────────
    const searchInput = document.getElementById('productSearch');
    const stockFilter = document.getElementById('stockFilter');
    const sortFilter = document.getElementById('sortFilter');
    const noResults = document.getElementById('no-search-results');
    const countLabel = document.getElementById('product-count-label');

    function filterAndSort() {
        const query = searchInput.value.toLowerCase().trim();
        const stockVal = stockFilter.value;
        const sortVal = sortFilter.value;

        let rows = Array.from(document.querySelectorAll('.product-row'));
        let visible = 0;

        rows.forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const stock = Number(row.dataset.stock || 0);
            const price = Number(row.dataset.price || 0);

            const matchName = !query || name.includes(query);
            const matchStock = stockVal === 'all' || (stockVal === 'low' && stock <= 5) || (stockVal === 'ok' && stock > 5);

            if (matchName && matchStock) {
                row.classList.remove('hidden');
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Sort
        const tbody = document.getElementById('products-tbody');
        if (tbody && sortVal !== 'default') {
            const tRows = Array.from(tbody.querySelectorAll('.product-row'));
            tRows.sort((a, b) => {
                if (sortVal === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sortVal === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
                if (sortVal === 'stock-asc') return Number(a.dataset.stock) - Number(b.dataset.stock);
                return 0;
            });
            tRows.forEach(r => tbody.appendChild(r));
        }

        if (noResults) noResults.classList.toggle('hidden', visible > 0);
        if (countLabel) countLabel.textContent = visible + ' item';
    }

    if (searchInput) searchInput.addEventListener('input', filterAndSort);
    if (stockFilter) stockFilter.addEventListener('change', filterAndSort);
    if (sortFilter) sortFilter.addEventListener('change', filterAndSort);

    // ── Real-time Updates ────────────────────────────
    const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

    const refreshLowStockCounter = () => {
        const lowStockEl = document.getElementById('low-stock-count');
        if (!lowStockEl) return;
        const rows = document.querySelectorAll('.product-row');
        let count = 0;
        rows.forEach(row => {
            if (Number(row.dataset.stock || 0) <= 5) count++;
        });
        lowStockEl.textContent = count;
    };

    const updateProductRow = (payload) => {
        if (!payload || !payload.id) return;
        const row = document.querySelector('[data-product-id="' + payload.id + '"]');
        if (!row) return;

        const stock = Number(payload.stock || 0);
        row.dataset.stock = stock;
        row.dataset.price = payload.price || row.dataset.price;

        const priceEl = row.querySelector('.product-price');
        const stockBadgeEl = row.querySelector('.product-stock-badge');
        const statusNoteEl = row.querySelector('.product-status-note');

        if (priceEl) priceEl.textContent = formatRupiah(payload.price);

        if (stockBadgeEl) {
            stockBadgeEl.textContent = stock + ' unit';
            stockBadgeEl.className = stockBadgeEl.className.replace(/bg-\w+-\d+ text-\w+-\d+/g, '').trim();
            if (stock > 5) stockBadgeEl.classList.add('bg-green-100', 'text-green-700');
            else if (stock > 0) stockBadgeEl.classList.add('bg-amber-100', 'text-amber-700');
            else stockBadgeEl.classList.add('bg-red-100', 'text-red-700');
        }

        if (statusNoteEl) {
            statusNoteEl.className = statusNoteEl.className.replace(/text-\w+-\d+|animate-pulse/g, '').trim();
            if (stock <= 5) {
                statusNoteEl.textContent = stock === 0 ? 'Habis!' : 'Restock!';
                statusNoteEl.classList.add('text-red-400', 'animate-pulse');
            } else {
                statusNoteEl.textContent = 'Aman';
                statusNoteEl.classList.add('text-green-500');
            }
        }

        // Pulse animation on update
        row.style.background = 'rgba(114,191,119,.08)';
        setTimeout(() => { row.style.background = ''; }, 1200);

        refreshLowStockCounter();
    };

    const getVisibleProductIds = () =>
        Array.from(document.querySelectorAll('.product-row'))
            .map(r => Number(r.getAttribute('data-product-id')))
            .filter(id => Number.isInteger(id));

    let lastSyncAt = null;
    const pollProductUpdates = async () => {
        const ids = getVisibleProductIds();
        if (!ids.length) return;
        const params = new URLSearchParams();
        ids.forEach(id => params.append('ids[]', String(id)));
        if (lastSyncAt) params.append('since', lastSyncAt);
        try {
            const response = await fetch('/api/products/updates?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                credentials: 'same-origin',
            });
            if (!response.ok) return;
            const result = await response.json();
            if (!result.success || !Array.isArray(result.data)) return;
            result.data.forEach(p => updateProductRow(p));
            lastSyncAt = new Date().toISOString();
        } catch (_) {}
    };

    if (broadcastKey && typeof window.Pusher !== 'undefined' && typeof window.Echo !== 'undefined') {
        const echoInstance = new window.Echo({
            broadcaster: 'pusher',
            key: broadcastKey,
            cluster: broadcastCluster,
            wsHost: broadcastHost,
            wsPort: broadcastPort,
            wssPort: broadcastPort,
            forceTLS: broadcastScheme === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': csrfToken } },
        });
        echoInstance.channel('products').listen('.ProductUpdated', updateProductRow);
    }

    pollProductUpdates();
    setInterval(() => { pollProductUpdates().catch(() => {}); }, 5000);
})();

// ── Delete Modal ──────────────────────────────
window.openDeleteModal = function(productId, productName) {
    const modal   = document.getElementById('delete-modal');
    const form    = document.getElementById('delete-form');
    const nameEl  = document.getElementById('delete-product-name');

    form.action = '/web/product/' + productId;
    if (nameEl) nameEl.textContent = productName;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

window.closeDeleteModal = function() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.closeDeleteModal();
});

document.getElementById('delete-modal')?.addEventListener('click', function(e) {
    if (e.target === this) window.closeDeleteModal();
});

// ── Toggle Product Active ──────────────────────────────
window.toggleProductActive = async function(productId, currentStatus) {
    console.log('Toggle called:', productId, currentStatus);
    
    // Show loading popup
    showPopup('loading', 'Memproses...', 'Mohon tunggu sebentar');
    
    try {
        const url = `/web/product/${productId}/toggle-active`;
        console.log('Fetching:', url);
        
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        console.log('Response status:', response.status);
        const result = await response.json();
        console.log('Result:', result);

        if (result.success) {
            showPopup('success', 'Berhasil!', result.message);
            // Reload halaman setelah 1 detik
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showPopup('error', 'Gagal!', result.message || 'Gagal mengubah status produk');
        }
    } catch (error) {
        console.error('Error:', error);
        showPopup('error', 'Error!', 'Terjadi kesalahan saat mengubah status produk');
    }
}

// ── Popup System ──────────────────────────────
window.showPopup = function(type, title, message) {
    // Remove existing popup
    const existing = document.getElementById('status-popup');
    if (existing) existing.remove();

    // Icon based on type
    let icon = '';
    let bgColor = '';
    let iconColor = '';
    
    if (type === 'success') {
        icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        bgColor = 'bg-green-100';
        iconColor = 'text-green-600';
    } else if (type === 'error') {
        icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        bgColor = 'bg-red-100';
        iconColor = 'text-red-600';
    } else if (type === 'loading') {
        icon = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';
        bgColor = 'bg-blue-100';
        iconColor = 'text-blue-600';
    }

    const popup = document.createElement('div');
    popup.id = 'status-popup';
    popup.className = 'fixed top-4 right-4 z-[9999] animate-slide-in-right';
    popup.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 flex items-center gap-4 min-w-[320px]">
            <div class="w-12 h-12 rounded-xl ${bgColor} flex items-center justify-center flex-shrink-0 ${iconColor}">
                ${icon}
            </div>
            <div class="flex-1">
                <p class="font-black text-gray-900 text-sm">${title}</p>
                <p class="text-xs text-gray-500 mt-0.5">${message}</p>
            </div>
            ${type !== 'loading' ? '<button onclick="closePopup()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>' : ''}
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto close after 3 seconds (except loading)
    if (type !== 'loading') {
        setTimeout(() => closePopup(), 3000);
    }
}

window.closePopup = function() {
    const popup = document.getElementById('status-popup');
    if (popup) {
        popup.style.animation = 'slide-out-right 0.3s ease';
        setTimeout(() => popup.remove(), 300);
    }
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in-right {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slide-out-right {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease;
    }
`;
document.head.appendChild(style);
rs: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (result.success) {
            // Reload halaman untuk update UI
            window.location.reload();
        } else {
            alert(result.message || 'Gagal mengubah status produk');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah status produk');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradeaaaa\resources\views/seller/products/index.blade.php ENDPATH**/ ?>