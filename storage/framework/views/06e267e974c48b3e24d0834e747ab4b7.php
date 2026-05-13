<?php $__env->startSection('title', 'Pesan Pembeli — Arradea Seller'); ?>
<?php $__env->startSection('page_title', 'Pesan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 lg:space-y-5 fade-up">

    
    <div class="relative overflow-hidden rounded-xl lg:rounded-3xl p-5 lg:p-6 lg:p-8" style="background:linear-gradient(135deg,#0f1a11 0%,#1e3a22 50%,#0f1a11 100%)">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10" style="background:#72bf77;filter:blur(60px)"></div>
        <div class="absolute -bottom-16 -left-8 w-48 h-48 rounded-full opacity-10" style="background:#4db85a;filter:blur(40px)"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 lg:gap-4">
            <div class="text-white">
                <p class="text-[9px] lg:text-[10px] font-black uppercase tracking-widest mb-1 lg:mb-2" style="color:#72bf77">Komunikasi</p>
                <h1 class="text-xl lg:text-2xl lg:text-3xl font-black tracking-tight">Kotak <span style="color:#a3e4a6">Pesan</span></h1>
                <p class="text-white/50 text-xs lg:text-sm mt-1 lg:mt-1.5">Kelola semua percakapan aktif dengan pembeli produk Anda.</p>
            </div>
            <div class="flex items-center gap-2 lg:gap-3 flex-shrink-0">
                <div class="text-center px-3 lg:px-5 py-2 lg:py-3 rounded-xl lg:rounded-2xl" style="background:rgba(114,191,119,.15);border:1px solid rgba(114,191,119,.2)">
                    <?php
                        $totalUnread = $chats->sum(function($chat) {
                            return $chat->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count();
                        });
                    ?>
                    <p class="text-lg lg:text-2xl font-black text-white"><?php echo e($totalUnread); ?></p>
                    <p class="text-[9px] lg:text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Belum Dibaca</p>
                </div>
                <div class="text-center px-3 lg:px-5 py-2 lg:py-3 rounded-xl lg:rounded-2xl" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <p class="text-lg lg:text-2xl font-black text-white"><?php echo e($chats->count()); ?></p>
                    <p class="text-[9px] lg:text-[10px] uppercase tracking-widest font-bold mt-0.5" style="color:#72bf77">Total Chat</p>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($totalUnread > 0): ?>
    <div class="flex items-center gap-2 lg:gap-3 p-3 lg:p-4 rounded-xl lg:rounded-2xl" style="background:#fef3c7;border:1px solid #fde68a">
        <span class="text-base lg:text-lg animate-pulse">💬</span>
        <p class="text-xs lg:text-sm font-black text-amber-800"><?php echo e($totalUnread); ?> pesan belum dibalas — jangan biarkan pembeli menunggu!</p>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 p-3 lg:p-4">
        <div class="relative">
            <svg class="absolute left-3 lg:left-3.5 top-1/2 -translate-y-1/2 w-3.5 lg:w-4 h-3.5 lg:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="chatSearch" placeholder="Cari nama pembeli..."
                   class="w-full h-9 lg:h-10 bg-gray-50 border border-gray-200 rounded-lg lg:rounded-xl pl-9 lg:pl-10 pr-3 lg:pr-4 text-xs lg:text-sm font-medium focus:outline-none focus:ring-2 focus:border-transparent transition"
                   style="--tw-ring-color:rgba(114,191,119,.4)">
        </div>
    </div>

    
    <div class="bg-white rounded-xl lg:rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-4 lg:px-5 py-3 lg:py-4 border-b border-gray-50">
            <h2 class="text-[11px] lg:text-sm font-black text-gray-700 uppercase tracking-widest">💬 Percakapan Aktif</h2>
            <span class="text-[10px] lg:text-xs font-bold text-gray-400"><?php echo e($chats->count()); ?> chat</span>
        </div>

        <div class="divide-y divide-gray-50" id="chat-list">
            <?php $__empty_1 = true; $__currentLoopData = $chats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $lastMsg = $chat->messages->first();
                $unread = $chat->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count();
                $buyerName = $chat->buyer->name ?? 'Pembeli';
                $orderInfo = $chat->order ?? null;
            ?>
            <a href="<?php echo e(route('chat.show', $chat->order_id)); ?>"
               class="chat-item block px-4 lg:px-5 py-3 lg:py-4 hover:bg-gray-50/60 transition-all duration-150 group"
               data-name="<?php echo e(strtolower($buyerName)); ?>">
                <div class="flex items-center gap-3 lg:gap-4">
                    
                    <div class="relative flex-shrink-0">
                        <div class="w-10 lg:w-11 h-10 lg:h-11 rounded-xl lg:rounded-2xl flex items-center justify-center text-xs lg:text-sm font-black transition-all group-hover:scale-105"
                             style="background:rgba(114,191,119,.12);color:#3fa348">
                            <?php echo e(strtoupper(substr($buyerName, 0, 1))); ?>

                        </div>
                        <?php if($unread > 0): ?>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[9px] font-black text-white flex items-center justify-center" style="background:#ef4444">
                            <?php echo e($unread > 9 ? '9+' : $unread); ?>

                        </span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="font-black text-gray-900 text-xs lg:text-sm truncate <?php echo e($unread > 0 ? '' : 'font-bold'); ?>">
                                <?php echo e($buyerName); ?>

                            </p>
                            <span class="text-[9px] lg:text-[10px] font-bold text-gray-400 flex-shrink-0">
                                <?php echo e($lastMsg ? $lastMsg->created_at->diffForHumans() : ''); ?>

                            </span>
                        </div>

                        <?php if($orderInfo): ?>
                        <p class="text-[9px] lg:text-[10px] font-bold uppercase tracking-widest mb-0.5 lg:mb-1" style="color:#72bf77">
                            Order #<?php echo e(str_pad($chat->order_id, 6, '0', STR_PAD_LEFT)); ?>

                            <?php if($orderInfo->product): ?>· <?php echo e(Str::limit($orderInfo->product->name ?? '', 28)); ?><?php endif; ?>
                        </p>
                        <?php endif; ?>

                        <p class="text-[11px] lg:text-xs text-gray-500 truncate <?php echo e($unread > 0 ? 'font-semibold text-gray-700' : ''); ?>">
                            <?php if($lastMsg): ?>
                                <?php if($lastMsg->sender_id === auth()->id()): ?>
                                    <span class="text-gray-400">Kamu: </span>
                                <?php endif; ?>
                                <?php echo e(Str::limit($lastMsg->message, 60)); ?>

                            <?php else: ?>
                                <span class="text-gray-300 italic">Belum ada pesan</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    
                    <svg class="w-3.5 lg:w-4 h-3.5 lg:h-4 text-gray-300 group-hover:text-green-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="flex flex-col items-center justify-center py-16 lg:py-20 text-center" id="empty-state">
                <div class="w-16 lg:w-20 h-16 lg:h-20 rounded-2xl lg:rounded-3xl flex items-center justify-center mb-4 lg:mb-5" style="background:rgba(114,191,119,.08)">
                    <svg class="w-8 lg:w-10 h-8 lg:h-10" style="color:#72bf77" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="font-black text-gray-900 text-sm lg:text-base">Belum Ada Percakapan</p>
                <p class="text-xs lg:text-sm text-gray-400 mt-1 max-w-xs">Percakapan dengan pembeli akan muncul di sini setelah mereka menghubungi Anda lewat halaman pesanan.</p>
            </div>
            <?php endif; ?>
        </div>

        
        <div id="no-results" class="hidden flex-col items-center justify-center py-12 text-center">
            <p class="font-black text-gray-700 text-sm">Tidak ditemukan</p>
            <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain.</p>
        </div>
    </div>
</div>

<script>
(function() {
    const searchInput = document.getElementById('chatSearch');
    const noResults   = document.getElementById('no-results');

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.chat-item');
        let visible = 0;

        items.forEach(item => {
            const name = (item.dataset.name || '');
            const match = !q || name.includes(q);
            item.classList.toggle('hidden', !match);
            if (match) visible++;
        });

        if (noResults) {
            noResults.classList.toggle('hidden', visible > 0);
            noResults.classList.toggle('flex', visible === 0 && items.length > 0);
        }
    });
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/seller/messages.blade.php ENDPATH**/ ?>