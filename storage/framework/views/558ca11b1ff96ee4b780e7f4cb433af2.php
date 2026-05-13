<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    
    <meta name="theme-color" content="#72bf77">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Arradea">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Arradea">
    
    
    <link rel="manifest" href="/manifest.json">
    
    
    <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-192x192.png">
    <title><?php echo $__env->yieldContent('title', 'Arradea Marketplace'); ?></title>
    
    
    <?php if(session('success')): ?>
        <meta name="flash-success" content="<?php echo e(session('success')); ?>">
    <?php endif; ?>
    <?php if(session('error')): ?>
        <meta name="flash-error" content="<?php echo e(session('error')); ?>">
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <meta name="flash-warning" content="<?php echo e(session('warning')); ?>">
    <?php endif; ?>
    <?php if(session('info')): ?>
        <meta name="flash-info" content="<?php echo e(session('info')); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-[#f7faf7] text-gray-900 font-sans antialiased" x-data="{ mobileOpen: false }">

    
    <nav class="sticky top-0 z-50 glass border-b border-green-100/60 shadow-sm shadow-green-100/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                
                <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2 group">
                    <img src="<?php echo e(asset('images/arradea.jpeg')); ?>" alt="Arradea" class="w-8 h-8 rounded-xl object-cover shadow-md shadow-green-300/40 group-hover:scale-105 transition">
                    <span class="text-xl font-black text-gray-900 tracking-tight">Arradea<span class="text-sage">.</span></span>
                </a>

                
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <svg class="absolute left-3.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" placeholder="Cari produk, toko..." class="w-full h-9 bg-gray-100/80 border border-gray-200/60 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-sage/40 focus:border-sage/60 transition-all">
                    </div>
                </div>

                
                <div class="hidden md:flex items-center gap-6">
                    <a href="<?php echo e(route('categories.index')); ?>" class="nav-link text-sm font-medium text-gray-600 hover:text-gray-900">Kategori</a>

                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->role !== 'admin'): ?>
                            <a href="<?php echo e(route('buyer.cart')); ?>" class="relative text-gray-500 hover:text-sage transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12m-10-5a2 2 0 104 0m6 0a2 2 0 104 0"/></svg>
                                <?php if(Auth::user()->carts->count() > 0): ?>
                                    <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-sage text-white text-[9px] font-black rounded-full flex items-center justify-center"><?php echo e(Auth::user()->carts->count()); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open=!open" class="flex items-center gap-2 hover:opacity-80 transition">
                                <div class="w-8 h-8 rounded-xl bg-sage/15 border border-sage/30 flex items-center justify-center text-sage font-black text-sm">
                                    <?php echo e(strtoupper(substr(Auth::user()->name,0,1))); ?>

                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="open && 'rotate-180'" style="transition:.2s" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open=false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50">
                                <div class="px-4 py-2.5 border-b border-gray-50">
                                    <p class="text-xs font-black text-gray-900 truncate"><?php echo e(Auth::user()->name); ?></p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5"><?php echo e(Auth::user()->is_seller ? 'Seller + Buyer' : 'Buyer'); ?></p>
                                </div>

                                
                                <?php if(Auth::user()->canSwitchToSellerMode()): ?>
                                    <div class="px-3 py-3 border-b border-gray-50">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-2 px-1">Mode Aktif</p>
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
                                <?php endif; ?>

                                <?php if(Auth::user()->is_seller): ?>
                                    <a href="<?php echo e(route('seller.dashboard')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Dashboard Seller
                                    </a>
                                <?php elseif(Auth::user()->role === 'admin'): ?>
                                    <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Admin Panel
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('buyer.dashboard')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Dashboard
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-sage transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil Saya
                                </a>
                                <div class="border-t border-gray-50 mt-1 pt-1">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Masuk</a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-primary text-white text-sm font-semibold px-4 py-2 rounded-xl">Daftar Gratis</a>
                    <?php endif; ?>
                </div>

                
                <button @click="mobileOpen=!mobileOpen" class="md:hidden p-2 rounded-xl hover:bg-gray-100 transition text-gray-600">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0" class="md:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-1">
            <div class="relative mb-3">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari produk..." class="w-full h-10 bg-gray-100 rounded-xl pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-sage/40">
            </div>
            <a href="<?php echo e(route('categories.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Kategori</a>
            <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center gap-3 px-3 py-3 bg-gray-50 rounded-xl mb-2">
                    <div class="w-9 h-9 rounded-xl bg-sage/15 flex items-center justify-center text-sage font-black"><?php echo e(strtoupper(substr(Auth::user()->name,0,1))); ?></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900"><?php echo e(Auth::user()->name); ?></p>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase"><?php echo e(Auth::user()->is_seller ? 'Seller' : 'Buyer'); ?></p>
                    </div>
                </div>
                <?php if(Auth::user()->role !== 'admin'): ?>
                    <a href="<?php echo e(route('buyer.cart')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12"/></svg>
                        Keranjang
                        <?php if(Auth::user()->carts->count() > 0): ?>
                            <span class="ml-auto bg-sage text-white text-[10px] font-black px-2 py-0.5 rounded-lg"><?php echo e(Auth::user()->carts->count()); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Profil Saya</a>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50">Keluar</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Masuk</a>
                <a href="<?php echo e(route('register')); ?>" class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-white btn-primary text-center">Daftar Gratis</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="min-h-[70vh]">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="<?php echo e(asset('images/arradea.jpeg')); ?>" alt="Arradea" class="w-7 h-7 rounded-lg object-cover shadow-sm">
                        <span class="text-lg font-black text-gray-900">Arradea<span class="text-sage">.</span></span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Marketplace warga Arradea. Belanja dari tetangga, untuk tetangga.</p>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Belanja</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="<?php echo e(route('categories.index')); ?>" class="hover:text-sage transition">Kategori</a></li>
                        <li><a href="<?php echo e(url('/')); ?>" class="hover:text-sage transition">Produk Terbaru</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Akun</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <?php if(auth()->guard()->check()): ?>
                            <li><a href="<?php echo e(route('profile')); ?>" class="hover:text-sage transition">Profil</a></li>
                            <?php if(!Auth::user()->is_seller && Auth::user()->role !== 'admin'): ?>
                                <li><a href="<?php echo e(route('seller.apply')); ?>" class="hover:text-sage transition">Jadi Seller</a></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('login')); ?>" class="hover:text-sage transition">Masuk</a></li>
                            <li><a href="<?php echo e(route('register')); ?>" class="hover:text-sage transition">Daftar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Seller</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="<?php echo e(route('seller.apply')); ?>" class="hover:text-sage transition">Daftar Seller</a></li>
                        <li><a href="<?php echo e(route('seller.dashboard')); ?>" class="hover:text-sage transition">Seller Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-gray-400">© <?php echo e(date('Y')); ?> Arradea Marketplace. Semua hak dilindungi.</p>
                <p class="text-xs text-gray-400">Dibuat dengan <span class="text-sage">♥</span> untuk warga Arradea</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function(){
        const base={background:'#fff',color:'#111827',customClass:{popup:'rounded-2xl shadow-2xl',title:'text-lg font-black',htmlContainer:'text-sm text-gray-500',confirmButton:'rounded-xl px-5 py-2.5 font-bold text-sm',cancelButton:'rounded-xl px-5 py-2.5 font-bold text-sm'},buttonsStyling:false};
        window.arradeaPopup={
            _fire(c){return typeof Swal!=='undefined'?Swal.fire({...base,...c}):null},
            success(msg,title){return this._fire({icon:'success',iconColor:'#72bf77',title:title||'✅ Berhasil',text:msg,confirmButtonColor:'#72bf77'})},
            error(msg,title){return this._fire({icon:'error',iconColor:'#dc2626',title:title||'❌ Gagal',text:msg,confirmButtonColor:'#dc2626'})},
            confirm(msg,opts={}){
                if(typeof Swal==='undefined')return Promise.resolve(false);
                return Swal.fire({...base,icon:'warning',iconColor:'#f59e0b',title:opts.title||'Konfirmasi',text:msg||'Lanjutkan?',showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, lanjut',cancelButtonText:'Batal',confirmButtonColor:opts.confirmColor||'#72bf77',reverseButtons:true}).then(r=>r.isConfirmed);
            },
            danger(msg,opts={}){
                if(typeof Swal==='undefined')return Promise.resolve(false);
                return Swal.fire({...base,icon:'warning',iconColor:'#dc2626',title:opts.title||'⚠️ Hapus?',text:msg,showCancelButton:true,confirmButtonText:opts.confirmText||'Ya, hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626',reverseButtons:true}).then(r=>r.isConfirmed);
            }
        };
        window.confirmSubmit=function(e,msg){
            e&&e.preventDefault();
            const form=e&&e.target;
            if(!form)return false;
            window.arradeaPopup.danger(msg).then(ok=>{if(ok)form.submit()});
            return false;
        };
    })();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    
    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ Service Worker registered:', registration.scope);
                    })
                    .catch(error => {
                        console.error('❌ Service Worker registration failed:', error);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        const installButton = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent default mini-infobar
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button if exists
            if (installButton) {
                installButton.style.display = 'flex';
            }
        });

        // Handle install button click
        if (installButton) {
            installButton.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                
                console.log(`User response: ${outcome}`);
                deferredPrompt = null;
                installButton.style.display = 'none';
            });
        }

        // Track if app is installed
        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA installed successfully!');
            deferredPrompt = null;
            if (installButton) {
                installButton.style.display = 'none';
            }
        });

        // Detect if running as PWA
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            console.log('🚀 Running as PWA');
            document.body.classList.add('pwa-mode');
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\arradea-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>