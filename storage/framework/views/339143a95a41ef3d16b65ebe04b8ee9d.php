<?php $__env->startSection('title', 'Pembayaran QRIS — Arradea'); ?>
<?php $__env->startSection('page_title', 'Pembayaran QRIS'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $carts = auth()->user()->carts()->with('product.store')->get();
    $totalOriginal = 0;
    $totalFinal = 0;

    foreach ($carts as $cart) {
        $pricing = $cart->product->calculatePricing($cart->variant_key, $cart->quantity);
        $cart->pricing = $pricing;
        $totalOriginal += $pricing['total_original'];
        $totalFinal += $pricing['total_final'];
    }
    
    $singleStoreIds = $carts->pluck('product.store_id')->unique();
    $singleStore = $singleStoreIds->count() === 1 ? $carts->first()?->product?->store : null;
    $singleSeller = $singleStore?->user;
    $hasQrisSeller = $singleSeller && $singleSeller->hasQrisPaymentSetup();
?>

<div class="max-w-2xl mx-auto space-y-4 fade-up">
    
    <a href="<?php echo e(route('buyer.cart')); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-900 transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Keranjang
    </a>

    <?php if(!$hasQrisSeller): ?>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
        <span class="text-4xl mb-3 block">⚠️</span>
        <h3 class="text-lg font-black text-red-900 mb-2">QRIS Tidak Tersedia</h3>
        <p class="text-sm text-red-700">Seller belum mengaktifkan pembayaran QRIS. Silakan gunakan metode COD.</p>
        <a href="<?php echo e(route('buyer.cart')); ?>" class="mt-4 inline-block px-6 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">
            Kembali ke Keranjang
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl lg:rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
        
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 lg:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white/80 uppercase tracking-wider">Pembayaran</p>
                    <h1 class="text-2xl lg:text-3xl font-black">QRIS Manual</h1>
                </div>
            </div>
            <p class="text-sm text-white/90">Scan QR code dan upload bukti pembayaran</p>
        </div>

        <div class="p-6 lg:p-8 space-y-6">
            
            <div class="bg-gradient-to-br from-primary-50 to-green-50 rounded-2xl p-6 border border-primary-100">
                <img src="<?php echo e(asset('storage/'.$singleSeller->qris_image)); ?>" 
                     alt="QRIS <?php echo e($singleSeller->payment_name); ?>" 
                     class="w-full max-w-sm mx-auto rounded-2xl shadow-xl bg-white border-4 border-white">
            </div>
            
            
            <div class="bg-gray-50 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <span class="text-sm font-bold text-gray-500">Penerima</span>
                    <span class="text-base font-black text-gray-900"><?php echo e($singleSeller->payment_name); ?></span>
                </div>
                <?php if($singleSeller->payment_type): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-500">Jenis Pembayaran</span>
                    <span class="text-sm font-bold text-gray-700"><?php echo e(strtoupper($singleSeller->payment_type)); ?></span>
                </div>
                <?php endif; ?>
                <?php if($singleSeller->payment_number): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-500">Nomor Akun</span>
                    <span class="text-sm font-bold text-gray-700"><?php echo e($singleSeller->payment_number); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <span class="text-sm font-bold text-gray-500">Total Pembayaran</span>
                    <span class="text-2xl font-black" style="color:#72bf77">Rp <?php echo e(number_format($totalFinal ?? 0,0,',','.')); ?></span>
                </div>
            </div>
            
            
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 font-black text-sm">
                        📱
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-blue-900 mb-3">Cara Pembayaran:</p>
                        <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                            <li>Buka aplikasi e-wallet Anda (GoPay, OVO, DANA, ShopeePay, dll)</li>
                            <li>Pilih menu <strong>Scan QR</strong> atau <strong>Bayar</strong></li>
                            <li>Scan QR code di atas</li>
                            <li>Pastikan nominal pembayaran <strong>Rp <?php echo e(number_format($totalFinal ?? 0,0,',','.')); ?></strong></li>
                            <li>Selesaikan pembayaran</li>
                            <li>Screenshot atau simpan bukti pembayaran</li>
                            <li>Upload bukti di form di bawah ini</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            
            <form action="<?php echo e(route('buyer.cart.checkout')); ?>" method="POST" id="qris-checkout-form" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="payment_method" value="qris">
                <input type="hidden" name="payment_proof_base64" id="payment-proof-base64">
                
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-3">
                        Upload Bukti Pembayaran <span class="text-red-400">*</span>
                    </label>
                    <input type="file" 
                           id="qris-proof-input" 
                           accept="image/*" 
                           required 
                           class="w-full px-4 py-4 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl focus:outline-none focus:border-primary-500 transition file:mr-4 file:px-5 file:py-3 file:rounded-xl file:border-0 file:bg-primary-600 file:text-white file:font-black file:text-sm hover:border-primary-400">
                    <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, WebP. Maksimal 4MB.</p>
                    
                    
                    <div id="qris-proof-preview" class="hidden mt-4 rounded-2xl border-2 border-gray-200 bg-gray-50 p-4">
                        <img id="qris-proof-preview-img" src="" alt="Preview" class="w-full rounded-xl shadow-md">
                        <button type="button" 
                                onclick="clearQrisProof()" 
                                class="mt-3 w-full py-3 text-sm font-bold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition">
                            🗑️ Hapus & Pilih Ulang
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-3">Catatan untuk Seller (Opsional)</label>
                    <textarea name="notes" 
                              rows="3" 
                              maxlength="1000"
                              class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 transition resize-none"
                              placeholder="Contoh: Mohon segera diproses, terima kasih"
                              style="--tw-ring-color:rgba(114,191,119,.4)"><?php echo e(old('notes')); ?></textarea>
                </div>
                
                
                <button type="submit" 
                        id="qris-submit-btn" 
                        class="w-full py-4 lg:py-5 rounded-2xl font-black text-base lg:text-lg text-white transition hover:opacity-90 active:scale-95 shadow-lg" 
                        style="background:#72bf77;box-shadow:0 6px 24px rgba(114,191,119,.4)">
                    ✅ Konfirmasi Pembayaran
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let qrisProofFile = null;

function clearQrisProof() {
    qrisProofFile = null;
    document.getElementById('qris-proof-input').value = '';
    document.getElementById('qris-proof-preview').classList.add('hidden');
}

// Preview image when selected
document.getElementById('qris-proof-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (4MB max)
        if (file.size > 4 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 4MB.');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
            alert('Format file tidak valid! Gunakan JPG, PNG, atau WebP.');
            this.value = '';
            return;
        }
        
        qrisProofFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qris-proof-preview-img').src = e.target.result;
            document.getElementById('qris-proof-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Form submit handler
document.getElementById('qris-checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!qrisProofFile) {
        alert('Silakan upload bukti pembayaran terlebih dahulu!');
        return;
    }
    
    const submitBtn = document.getElementById('qris-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Memproses Pembayaran...';
    
    // Convert file to base64
    const reader = new FileReader();
    reader.onload = function(e) {
        const base64 = e.target.result;
        document.getElementById('payment-proof-base64').value = base64;
        
        // Submit form
        document.getElementById('qris-checkout-form').submit();
    };
    reader.readAsDataURL(qrisProofFile);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/buyer/cart/qris.blade.php ENDPATH**/ ?>