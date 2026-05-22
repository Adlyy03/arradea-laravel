<?php $__env->startSection('title', 'Order Masuk - Arradea Seller'); ?>
<?php $__env->startSection('page_title', 'Order Masuk'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5 fade-up">

    
    <div class="relative overflow-hidden rounded-3xl p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-16 -left-8 w-48 h-48 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#72bf77">Manajemen Pesanan</p>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">Order <span style="color:#a3e4a6">Masuk</span> Toko</h1>
                <p class="text-white/50 text-sm mt-1.5">Konfirmasi dan kelola setiap pesanan dari pembeli.</p>
            </div>

            
            <div class="flex gap-3 w-full lg:w-auto">
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.2)">
                    <p class="text-2xl font-black text-amber-400"><?php echo e($pendingCount); ?></p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-amber-300/70 mt-0.5">Pending</p>
                </div>
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.2)">
                    <p class="text-2xl font-black text-blue-400"><?php echo e($orders->where('status','processing')->count()); ?></p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-blue-300/70 mt-0.5">Diproses</p>
                </div>
                <div class="flex-1 lg:flex-none text-center px-5 py-3 rounded-2xl" style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2)">
                    <p class="text-2xl font-black text-green-400"><?php echo e($doneCount); ?></p>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-green-300/70 mt-0.5">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($pendingCount > 0): ?>
    <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-2xl">
        <div class="flex items-center gap-3">
            <span class="text-xl animate-bounce">⚡</span>
            <div>
                <p class="text-sm font-black text-amber-800"><?php echo e($pendingCount); ?> pesanan menunggu konfirmasi Anda</p>
                <p class="text-xs text-amber-600">Respon cepat meningkatkan rating toko Anda.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <?php $activeStatus = request('status'); ?>
        <a href="<?php echo e(route('seller.orders')); ?>"
           class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition <?php echo e(!$activeStatus ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>"
           style="<?php echo e(!$activeStatus ? 'background:#72bf77' : ''); ?>">Semua</a>
        <?php $__currentLoopData = ['pending'=>'⏳ Pending','processing'=>'🔄 Diproses','shipped'=>'🚚 Dikirim','completed'=>'✅ Selesai','cancelled'=>'❌ Dibatalkan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('seller.orders', ['status'=>$key])); ?>"
           class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition <?php echo e($activeStatus===$key ? 'text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>"
           style="<?php echo e($activeStatus===$key ? 'background:#72bf77' : ''); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">🛒 Daftar Pesanan</h2>
            <span class="text-xs font-bold text-gray-400">Total: <?php echo e($orders->total()); ?></span>
        </div>

        
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/80 text-[10px] font-black tracking-widest uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-4">Pembeli</th>
                        <th class="px-5 py-4">Produk</th>
                        <th class="px-5 py-4">Qty / Total</th>
                        <th class="px-5 py-4">Catatan</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusMap = [
                            'pending'    => ['Menunggu',  'bg-amber-100 text-amber-700'],
                            'processing' => ['Diproses',  'bg-blue-100 text-blue-700'],
                            'shipped'    => ['Dikirim',   'bg-purple-100 text-purple-700'],
                            'completed'  => ['Selesai',   'bg-green-100 text-green-700'],
                            'cancelled'  => ['Dibatalkan','bg-gray-100 text-gray-500'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500'];
                    ?>
                    <tr class="hover:bg-gray-50/60 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0"
                                     style="background:rgba(114,191,119,.1);color:#3fa348">
                                    <?php echo e(strtoupper(substr($order->user->name ?? '?', 0, 1))); ?>

                                </div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm"><?php echo e($order->user->name ?? 'Pembeli'); ?></p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ARRD-<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <?php if($order->product?->image): ?>
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                    <img src="<?php echo e($order->product->image); ?>" alt="" class="w-full h-full object-cover">
                                </div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm"><?php echo e($order->product->name ?? '—'); ?></p>
                                    <?php $variantName = data_get($order->product?->getVariant($order->variant_key), 'name', null); ?>
                                    <?php if($variantName && $variantName !== 'Default'): ?>
                                    <p class="text-[10px] text-gray-400 font-bold"><?php echo e($variantName); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-black text-gray-900"><?php echo e($order->quantity ?? 1); ?>×</p>
                            <?php if(($order->discount_percent_applied ?? 0) > 0 && $order->unit_price_original): ?>
                            <p class="text-[10px] text-gray-400 line-through">Rp <?php echo e(number_format($order->unit_price_original * $order->quantity, 0, ',', '.')); ?></p>
                            <?php endif; ?>
                            <p class="text-sm font-black" style="color:#3fa348">Rp <?php echo e(number_format($order->total_price, 0, ',', '.')); ?></p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 max-w-[160px] line-clamp-2"><?php echo e($order->notes ?: '—'); ?></p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="<?php echo e($statusClass); ?> px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                <?php echo e($statusLabel); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <?php if($order->status === 'pending'): ?>
                                    <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-white transition hover:opacity-80 active:scale-95"
                                                style="background:#72bf77">✓ Terima</button>
                                    </form>
                                    <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-red-600 bg-red-100 hover:bg-red-200 transition active:scale-95">✕ Tolak</button>
                                    </form>
                                <?php elseif($order->status === 'processing'): ?>
                                    <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="status" value="shipped">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-white bg-purple-600 hover:bg-purple-700 transition active:scale-95">🚚 Kirim</button>
                                    </form>
                                <?php elseif($order->status === 'shipped'): ?>
                                    <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-black text-white bg-green-600 hover:bg-green-700 transition active:scale-95">✓ Selesai</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-gray-300 font-bold">—</span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('chat.show', $order)); ?>"
                                   class="px-3 py-1.5 rounded-lg text-xs font-black text-white bg-gray-700 hover:bg-gray-800 transition active:scale-95">
                                    💬 Chat
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-5xl">📭</span>
                                <p class="font-black text-gray-900 text-lg">Belum Ada Pesanan</p>
                                <p class="text-sm text-gray-400">Pesanan dari pembeli akan muncul di sini secara otomatis.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="lg:hidden divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $statusMap = [
                    'pending'    => ['Menunggu',  'bg-amber-100 text-amber-700'],
                    'processing' => ['Diproses',  'bg-blue-100 text-blue-700'],
                    'shipped'    => ['Dikirim',   'bg-purple-100 text-purple-700'],
                    'completed'  => ['Selesai',   'bg-green-100 text-green-700'],
                    'cancelled'  => ['Dibatalkan','bg-gray-100 text-gray-500'],
                ];
                [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500'];
            ?>
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black" style="background:rgba(114,191,119,.1);color:#3fa348">
                            <?php echo e(strtoupper(substr($order->user->name ?? '?', 0, 1))); ?>

                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-sm"><?php echo e($order->user->name ?? 'Pembeli'); ?></p>
                            <p class="text-[10px] font-bold text-gray-400">ARRD-<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?></p>
                        </div>
                    </div>
                    <span class="<?php echo e($statusClass); ?> px-2.5 py-1 rounded-lg text-[10px] font-black uppercase"><?php echo e($statusLabel); ?></span>
                </div>

                <div class="flex gap-3 p-3 bg-gray-50 rounded-xl">
                    <?php if($order->product?->image): ?>
                    <img src="<?php echo e($order->product->image); ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                    <?php endif; ?>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate"><?php echo e($order->product->name ?? '—'); ?></p>
                        <p class="text-xs text-gray-400">Qty: <?php echo e($order->quantity); ?>× · <span class="font-black" style="color:#3fa348">Rp <?php echo e(number_format($order->total_price, 0, ',', '.')); ?></span></p>
                    </div>
                </div>

                <?php if($order->notes): ?>
                <p class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">📝 <?php echo e($order->notes); ?></p>
                <?php endif; ?>

                <div class="flex gap-2 flex-wrap">
                    <?php if($order->status === 'pending'): ?>
                        <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-black text-white transition hover:opacity-80" style="background:#72bf77">✓ Terima Order</button>
                        </form>
                        <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="py-2 px-4 rounded-xl text-xs font-black text-red-600 bg-red-100 hover:bg-red-200 transition">Tolak</button>
                        </form>
                    <?php elseif($order->status === 'processing'): ?>
                        <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-black text-white bg-purple-600 hover:bg-purple-700 transition">🚚 Tandai Dikirim</button>
                        </form>
                    <?php elseif($order->status === 'shipped'): ?>
                        <form action="/web/order/<?php echo e($order->id); ?>/status" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-black text-white bg-green-600 hover:bg-green-700 transition">✓ Tandai Selesai</button>
                        </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('chat.show', $order)); ?>" class="py-2 px-4 rounded-xl text-xs font-black text-white bg-gray-700 hover:bg-gray-800 transition">💬 Chat</a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <span class="text-5xl">📭</span>
                <p class="font-black text-gray-900 mt-3">Belum Ada Pesanan</p>
                <p class="text-sm text-gray-400 mt-1">Pesanan dari pembeli akan muncul di sini.</p>
            </div>
            <?php endif; ?>
        </div>

        
        <?php if($orders->hasPages()): ?>
        <div class="px-5 py-4 border-t border-gray-50">
            <?php echo e($orders->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/seller/orders/index.blade.php ENDPATH**/ ?>