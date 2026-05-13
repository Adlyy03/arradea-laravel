<?php $__env->startSection('title', 'Manajemen Pengguna — Arradea Admin'); ?>
<?php $__env->startSection('page_title', 'Semua Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5 fade-up" x-data="{ openEditModal: false, editUser: null }">

    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Data <span style="color:#72bf77">Pengguna</span></h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh akun pembeli, penjual, dan admin.</p>
        </div>
        <div class="flex items-center gap-2 p-3 rounded-2xl border border-gray-100 bg-white">
            <span class="text-sm font-black text-gray-900"><?php echo e($users->total()); ?></span>
            <span class="text-xs text-gray-400 font-medium">total akun</span>
        </div>
    </div>

    
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?php echo e(!request('type') ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>" style="<?php echo e(!request('type') ? 'background:#72bf77' : ''); ?>">Semua</a>
        <a href="<?php echo e(route('admin.users.index',['type'=>'buyer'])); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?php echo e(request('type')==='buyer' ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>" style="<?php echo e(request('type')==='buyer' ? 'background:#72bf77' : ''); ?>">Buyer</a>
        <a href="<?php echo e(route('admin.users.index',['type'=>'seller'])); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?php echo e(request('type')==='seller' ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>" style="<?php echo e(request('type')==='seller' ? 'background:#72bf77' : ''); ?>">Seller</a>
        <a href="<?php echo e(route('admin.users.index',['type'=>'admin'])); ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?php echo e(request('type')==='admin' ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'); ?>" style="<?php echo e(request('type')==='admin' ? 'background:#72bf77' : ''); ?>">Admin</a>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Pengguna</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden sm:table-cell">Tipe</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden md:table-cell">Verifikasi</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden lg:table-cell">Bergabung</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $isVerified = $user->role === 'admin' || ($user->accessCode && $user->accessCode->is_active); ?>
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0" style="background:rgba(114,191,119,.12);color:#3fa348"><?php echo e(strtoupper(substr($user->name,0,1))); ?></div>
                                <div>
                                    <p class="font-bold text-gray-900"><?php echo e($user->name); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo e($user->phone); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase <?php echo e($user->role==='admin' ? 'bg-purple-100 text-purple-700' : ($user->is_seller ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')); ?>">
                                <?php echo e($user->role==='admin' ? 'Admin' : ($user->is_seller ? 'Seller' : 'Buyer')); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase <?php echo e($isVerified ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'); ?>">
                                <?php echo e($isVerified ? '✓ Verified' : '⏳ Pending'); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 hidden lg:table-cell"><?php echo e($user->created_at->format('d M Y')); ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <?php if(!$isVerified && $user->role !== 'admin'): ?>
                                <form method="POST" action="<?php echo e(route('admin.users.verify', $user)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-white transition hover:opacity-80" style="background:#72bf77">Verif.</button>
                                </form>
                                <?php endif; ?>
                                <button type="button" @click="openEditModal=true; editUser=<?php echo e(json_encode($user)); ?>"
                                    class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Edit</button>
                                <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" onsubmit="return confirmSubmit(event, <?php echo \Illuminate\Support\Js::from('Hapus akun '.$user->name.'?')->toHtml() ?>)">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-500 hover:bg-red-100 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-50"><?php echo e($users->links()); ?></div>
    </div>

    
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div @click.away="openEditModal=false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-3xl w-full max-w-md p-7 relative shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">Edit Pengguna</h3>
                <button @click="openEditModal=false" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="`/admin/users/${editUser?.id}`" class="space-y-4">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editUser.name" required class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nomor HP</label>
                    <input type="text" name="phone" x-model="editUser.phone" required class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition" style="--tw-ring-color:rgba(114,191,119,.4)">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Mode Seller</label>
                    <select name="is_seller" x-model="editUser.is_seller" required class="w-full h-11 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-medium focus:outline-none focus:ring-2 transition">
                        <option value="0">Buyer Saja</option>
                        <option value="1">Seller + Buyer</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1.5">*Mode seller hanya memengaruhi akses jual.</p>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full h-11 rounded-xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\arradea-laravel\resources\views/admin/users.blade.php ENDPATH**/ ?>