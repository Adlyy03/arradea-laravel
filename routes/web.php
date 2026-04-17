<?php

use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AdminUserController;
use App\Models\AccessCode;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Middleware\SyncSellerStoreSchedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;


// ─────────────────────────────────────────────────────────────────────────────
// Arradea Marketplace — Web Routes (Final Production-Ready Interface)
// ─────────────────────────────────────────────────────────────────────────────

// ADMIN: Migration helper (delete after use)
Route::get('/run-migrations', function () {
    if (!env('APP_DEBUG')) {
        return response('Not allowed', 403);
    }
    
    try {
        Artisan::call('migrate', ['--force' => true]);
        return '<pre style="white-space: pre-wrap; word-wrap: break-word;">' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<pre>Error: ' . $e->getMessage() . '</pre>';
    }
});

// PUBLIC & GUEST
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',    fn() => view('auth.login'))->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/login',    [AuthWebController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    // ── OTP Verification (setelah register, user belum login) ──────────────
    Route::get('/phone/verify', function () {
        if (!session('register_phone')) {
            return redirect()->route('login');
        }
        return view('auth.verify-phone');
    })->name('verification.phone.notice');

    Route::post('/phone/verify', function (Request $request) {
        $phone = session('register_phone');

        if (!$phone) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Kode verifikasi harus diisi.',
            'code.size'     => 'Kode verifikasi harus 6 digit.',
            'code.regex'    => 'Kode verifikasi hanya boleh berisi angka.',
        ]);

        $otp = \App\Models\Otp::where('phone', $phone)
            ->where('verified_at', null)
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.']);
        }

        if ($otp->isExpired()) {
            return back()->withErrors(['code' => 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.']);
        }

        if ($otp->attempts >= 5) {
            return back()->withErrors(['code' => 'Terlalu banyak percobaan salah. Silakan minta kode baru.']);
        }

        if (!$otp->verify($request->code)) {
            return back()->withErrors(['code' => '❌ OTP Salah. Coba lagi.']);
        }

        // OTP benar — tandai nomor sebagai terverifikasi
        $user = \App\Models\User::where('phone', $phone)->first();
        if ($user) {
            $user->update(['phone_verified_at' => now()]);
        }

        // Simpan session untuk halaman approval (jangan hapus dulu)
        // session()->forget('register_phone'); // <- hapus SETELAH approval page ditampilkan

        return redirect()->route('verification.admin.approval');
    })->name('verification.phone.verify');

    Route::get('/phone/admin-approval', function () {
        return view('auth.verify-admin-approval');
    })->name('verification.admin.approval');

    Route::post('/phone/verify/resend', function (Request $request) {
        $phone = session('register_phone');

        if (!$phone) {
            return redirect()->route('login');
        }

        $otp = \App\Models\Otp::createForPhone($phone);

        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
            ->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => "Kode verifikasi baru untuk Arradea:\n\n*{$otp->code}*\n\n_Kode berlaku 10 menit. Jangan bagikan kode ini ke siapa pun._",
            ]);

        return back()->with('status', '✅ Kode verifikasi baru telah dikirim ke WhatsApp kamu.');
    })->name('verification.phone.resend');
});


// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//     ->middleware(['auth', 'signed', 'throttle:6,1'])
//     ->name('verification.verify');

// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//     ->middleware(['auth', 'throttle:6,1'])
//     ->name('verification.send');

// ─────────────────────────────────────────────────────────────────────────────
// PROTECTED DASHBOARDS (Session-Based Auth)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'arradea.access', 'phone.verified', SyncSellerStoreSchedule::class])->group(function () {

    // 👨💼 ADMIN
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'));
        Route::get('/sellers',   fn() => view('admin.sellers'));
    });

    // 🏪 SELLER
    Route::middleware('role:seller')->prefix('seller')->group(function () {
        Route::get('/dashboard', fn() => view('seller.dashboard'))->name('seller.dashboard');
        Route::post('/store-status', [AuthWebController::class, 'toggleStoreStatus'])->name('seller.store-status');
        Route::post('/store-schedule', [AuthWebController::class, 'updateStoreSchedule'])->name('seller.store-schedule');
        
        // Products CRUD List
        Route::get('/products', function () {
            $products = auth()->user()->store ? auth()->user()->store->products()->latest()->get() : collect();
            return view('seller.products.index', compact('products'));
        })->name('seller.products');

        // Products Create/Edit UI
        Route::get('/products/create', fn() => view('seller.products.create'))->name('seller.products.create');
        Route::get('/products/{id}/edit', function ($id) {
            $product = Product::findOrFail($id);
            return view('seller.products.create', compact('product'));
        })->name('seller.products.edit');

        Route::get('/orders', function () {
            $store  = auth()->user()->store;
            $orders = $store
                ? $store->orders()->with(['user', 'product'])->latest()->paginate(15)
                : collect()->paginate(0);
            $pendingCount = $store ? $store->orders()->where('status', 'pending')->count() : 0;
            $doneCount    = $store ? $store->orders()->where('status', 'done')->count() : 0;
            return view('seller.orders.index', compact('orders', 'pendingCount', 'doneCount'));
        })->name('seller.orders');

        // New Features
        Route::get('/analytics', function () {
            $store = auth()->user()->store;
            $analytics = [
                'total_products' => $store ? $store->products()->count() : 0,
                'total_orders' => $store ? $store->orders()->count() : 0,
                'pending_orders' => $store ? $store->orders()->where('status', 'pending')->count() : 0,
                'completed_orders' => $store ? $store->orders()->where('status', 'done')->count() : 0,
                'total_revenue' => $store ? $store->orders()->where('status', 'done')->sum('total_price') : 0,
                'monthly_orders' => $store ? $store->orders()->whereMonth('created_at', now()->month)->count() : 0,
            ];
            return view('seller.analytics', compact('analytics'));
        })->name('seller.analytics');

        Route::get('/analytics/export', function () {
            $store = auth()->user()->store;
            if (!$store) abort(403, 'Akses Ditolak');

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=laporan_penjualan_{$store->name}_" . date('Y-m-d') . ".csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $orders = $store->orders()->with(['user', 'product'])->latest()->get();

            $callback = function() use($orders) {
                $file = fopen('php://output', 'w');
                // CSV Header
                fputcsv($file, ['ID Pesanan', 'Tanggal', 'Nama Pembeli', 'Produk', 'Jumlah', 'Total Harga', 'Status']);

                foreach ($orders as $order) {
                    $statusLabel = [
                        'pending' => 'Menunggu',
                        'accepted' => 'Diproses',
                        'done' => 'Selesai',
                        'rejected' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                    ][$order->status] ?? $order->status;

                    fputcsv($file, [
                        'ARRD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                        \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i'),
                        $order->user->name ?? 'Pembeli',
                        $order->product->name ?? 'Produk Dihapus',
                        $order->quantity,
                        $order->total_price,
                        $statusLabel
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('seller.analytics.export');

        Route::get('/messages', function () {
            $chats = \App\Models\Chat::where('seller_id', auth()->id())->with(['buyer', 'messages' => function($q) {
                $q->latest()->take(1);
            }])->get();
            return view('seller.messages', compact('chats'));
        })->name('seller.messages');

        Route::get('/settings', fn() => view('seller.settings'))->name('seller.settings');
    });

    Route::get('/seller/apply', function () {
        $user = auth()->user();
        return view('seller.apply', ['user' => $user]);
    })->name('seller.apply');

    // Step 1: Buyer isi form toko → buat/update data toko (pending) → kirim OTP → redirect ke verify
    Route::post('/seller/apply', function (Request $request) {
        $request->validate([
            'store_name'        => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_address'     => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        // Langsung simpan/update data toko dengan status pending agar data tidak hilang
        $storeData = [
            'name'        => $request->store_name,
            'description' => $request->store_description,
            'address'     => $request->store_address,
            'status'      => 'pending',
        ];

        if ($user->store) {
            $user->store->update($storeData);
        } else {
            $user->store()->create($storeData);
        }

        // Generate & kirim OTP ke nomor HP buyer yang sudah login
        $otp = \App\Models\Otp::createForPhone($user->phone);

        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
            ->post('https://api.fonnte.com/send', [
                'target'  => $user->phone,
                'message' => "Halo {$user->name}!\n\nKamu mengajukan diri jadi Seller di Arradea.\n\nKode OTP verifikasi kamu:\n\n*{$otp->code}*\n\n_Kode berlaku 10 menit. Jangan bagikan ke siapa pun._",
            ]);

        // Tandai bahwa user sedang dalam proses upgrade seller
        $user->update([
            'seller_status'     => 'pending',
            'seller_applied_at' => now(),
        ]);

        return redirect()->route('seller.verify-otp');
    })->name('seller.apply.store');

    // Step 2: Halaman input OTP (harus login)
    Route::get('/seller/verify-otp', function () {
        $user = auth()->user();
        if ($user->seller_status !== 'pending') {
            return redirect()->route('seller.apply')->with('info', 'Silakan isi data toko terlebih dahulu.');
        }
        return view('seller.verify-otp', compact('user'));
    })->name('seller.verify-otp');

    // Step 3: Proses verifikasi OTP seller
    Route::post('/seller/verify-otp', function (Request $request) {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Kode OTP harus diisi.',
            'code.size'     => 'Kode OTP harus 6 digit.',
            'code.regex'    => 'Kode OTP hanya boleh berisi angka.',
        ]);

        $user = auth()->user();
        $otp  = \App\Models\Otp::where('phone', $user->phone)
            ->where('verified_at', null)
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'OTP tidak ditemukan. Coba kirim ulang.']);
        }
        if ($otp->isExpired()) {
            return back()->withErrors(['code' => 'OTP sudah kadaluarsa. Coba kirim ulang.']);
        }
        if ($otp->attempts >= 5) {
            return back()->withErrors(['code' => 'Terlalu banyak percobaan. Kirim ulang kode OTP.']);
        }
        if (!$otp->verify($request->code)) {
            return back()->withErrors(['code' => '❌ OTP Salah. Coba lagi.']);
        }

        // Tandai OTP sudah diverifikasi, nunggu approval admin
        $user->update(['seller_otp_verified' => true]);

        return redirect()->route('seller.pending');
    })->name('seller.verify-otp.submit');

    // Step 4: Halaman nunggu approval admin
    Route::get('/seller/pending', function () {
        $user = auth()->user();
        if ($user->is_seller) {
            return redirect()->route('seller.dashboard')->with('success', 'Akun seller kamu sudah aktif!');
        }
        return view('seller.pending', compact('user'));
    })->name('seller.pending');

    // Kirim ulang OTP seller
    Route::post('/seller/verify-otp/resend', function (Request $request) {
        $user = auth()->user();
        $otp  = \App\Models\Otp::createForPhone($user->phone);

        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
            ->post('https://api.fonnte.com/send', [
                'target'  => $user->phone,
                'message' => "Kode OTP baru untuk upgrade Seller di Arradea:\n\n*{$otp->code}*\n\n_Kode berlaku 10 menit._",
            ]);

        return back()->with('status', '✅ Kode OTP baru telah dikirim ke WhatsApp kamu.');
    })->name('seller.verify-otp.resend');


    // 🛒 BUYER
    Route::prefix('buyer')->group(function () {
        Route::get('/dashboard', function () {
            $totalOrders = auth()->user()->orders()->count();
            $pendingOrders = auth()->user()->orders()->whereIn('status', ['pending', 'accepted'])->count();
            $completedOrders = auth()->user()->orders()->where('status', 'done')->count();
            $cartCount = auth()->user()->carts->count();
            return view('buyer.dashboard', compact('totalOrders', 'pendingOrders', 'completedOrders', 'cartCount'));
        })->name('buyer.dashboard');

        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('buyer.cart');
        Route::post('/cart', [CartController::class, 'store'])->name('buyer.cart.store');
        Route::put('/cart/{cart}', [CartController::class, 'update'])->name('buyer.cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('buyer.cart.destroy');
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('buyer.cart.checkout');

        // Orders routes
        Route::get('/orders', fn() => view('buyer.orders'))->name('buyer.orders');
        Route::get('/orders/{order}', function (Order $order) {
            abort_if($order->user_id !== auth()->id(), 403);
            return view('buyer.orders.show', ['order' => $order->load(['product', 'store', 'chat'])]);
        })->name('buyer.orders.show');

        // Wishlist routes
        Route::get('/wishlist', fn() => view('buyer.wishlist'))->name('buyer.wishlist');
        Route::post('/wishlist/{product}', fn(Product $product) => auth()->user()->wishlists()->toggle($product->id))->name('buyer.wishlist.toggle');

        // Wishlist routes
        Route::get('/wishlist', fn() => view('buyer.wishlist'))->name('buyer.wishlist');
        Route::post('/wishlist/{product}', fn(Product $product) => auth()->user()->wishlists()->toggle($product->id))->name('buyer.wishlist.toggle');
    });

    // Public product routes (anyone can view products)
    Route::get('/products', function (\Illuminate\Http\Request $request) {
        $query = Product::with('store', 'category')
            ->whereHas('store.user', function ($userQuery) {
                $userQuery->where('role', 'seller')->where('store_status', 'open');
            })
            ->latest();
        $keyword = trim((string) $request->query('q', ''));

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }
        
        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::all();
        
        return view('buyer.products.index', compact('products', 'categories', 'keyword'));
    })->name('buyer.products');

    Route::get('/products/{id}', function ($id) {
        $product = Product::with('store')
            ->whereHas('store.user', function ($userQuery) {
                $userQuery->where('role', 'seller')->where('store_status', 'open');
            })
            ->findOrFail($id);
        return view('buyer.products.show', compact('product'));
    })->name('buyer.products.show');

    // Web order submission (buyer only)
    Route::post('/web/order', [OrderController::class, 'store'])->name('web.order.store');

    // Categories routes (public)
    Route::get('/categories', function () {
        $categories = \App\Models\Category::parents()->with('children')->orderBy('sort_order')->get();
        return view('categories.index', compact('categories'));
    })->name('categories.index');

    Route::get('/categories/{category}', function (\App\Models\Category $category) {
        $products = $category->products()
            ->with('store')
            ->whereHas('store.user', function ($userQuery) {
                $userQuery->where('role', 'seller')->where('store_status', 'open');
            })
            ->paginate(20);
        return view('categories.show', compact('category', 'products'));
    })->name('categories.show');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/map-users', [AdminUserController::class, 'mapUsers'])->name('admin.map-users');

        // New verification routes with AdminUserController
        Route::get('/users-verification', [AdminUserController::class, 'index'])->name('admin.users.verification');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('admin.users.approve');
        Route::post('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('admin.users.reject');

        // Old seller routes (keep for compatibility)
        Route::post('/sellers/{user}/approve', function (User $user) {
            $user->update([
                'is_seller' => true,
                'seller_status' => 'approved',
                'seller_approved_at' => now(),
                'seller_rejected_at' => null,
                'seller_rejection_reason' => null,
            ]);

            if ($user->store) {
                $user->store->update([
                    'status' => 'active',
                    'approved_at' => now(),
                ]);
            }

            $user->notify(new \App\Notifications\SellerApplicationNotification('approved'));

            return redirect('/admin/sellers')->with('success', 'Seller berhasil disetujui.');
        })->name('admin.sellers.approve');

        Route::post('/sellers/{user}/reject', function (Request $request, User $user) {
            $request->validate([
                'reason' => ['nullable', 'string', 'max:1000'],
            ]);

            $user->update([
                'is_seller' => false,
                'seller_status' => 'rejected',
                'seller_rejected_at' => now(),
                'seller_rejection_reason' => $request->reason,
            ]);

            if ($user->store) {
                $user->store->update(['status' => 'rejected']);
            }

            $user->notify(new \App\Notifications\SellerApplicationNotification('rejected', $request->reason));

            return redirect('/admin/sellers')->with('success', 'Permohonan seller ditolak.');
        })->name('admin.sellers.reject');

        // Verification (New Users & Seller Upgrades)
        Route::get('/verifications', function () {
            // Pendaftar buyer baru: phone verified but no access_code_id
            $pendingBuyers = User::whereNotNull('phone_verified_at')
                                 ->whereNull('access_code_id')
                                 ->where('role', '!=', 'admin')
                                 ->where('seller_otp_verified', false)
                                 ->latest()
                                 ->paginate(20, ['*'], 'buyers_page');

            // Calon seller: buyer existing yang sudah verify OTP seller upgrade
            $pendingSellers = User::whereNotNull('phone_verified_at')
                                  ->whereNotNull('access_code_id')
                                  ->where('seller_otp_verified', true)
                                  ->where('is_seller', false)
                                  ->latest()
                                  ->paginate(20, ['*'], 'sellers_page');

            return view('admin.verifications', compact('pendingBuyers', 'pendingSellers'));
        })->name('admin.verifications.index');

        // Approve buyer baru
        Route::post('/verifications/{user}/approve', function (User $user) {
            $ac = AccessCode::where('is_active', true)->first();
            if (!$ac) {
                return back()->withErrors(['message' => 'Tidak ada Kode Akses aktif! Buat terlebih dahulu di menu Kode Akses.']);
            }
            $user->update(['access_code_id' => $ac->id]);
            return back()->with('success', "Buyer {$user->name} berhasil disetujui dan sekarang dapat login.");
        })->name('admin.verifications.approve');

        // Tolak buyer baru (hapus akun)
        Route::post('/verifications/{user}/reject', function (User $user) {
            $user->delete();
            return back()->with('success', 'Pendaftaran pengguna ditolak dan data telah dihapus.');
        })->name('admin.verifications.reject');

        // Approve upgrade seller
        Route::post('/verifications/{user}/approve-seller', function (User $user) {
            $user->update([
                'is_seller'           => true,
                'seller_status'       => 'approved',
                'seller_approved_at'  => now(),
                'seller_rejected_at'  => null,
                'seller_rejection_reason' => null,
                'seller_otp_verified' => false,
            ]);
            if ($user->store) {
                $user->store->update(['status' => 'active', 'approved_at' => now()]);
            }
            return back()->with('success', "Seller {$user->name} berhasil disetujui! Mereka sekarang bisa berjualan.");
        })->name('admin.verifications.approve-seller');

        // Tolak upgrade seller (reset status, hapus toko pending)
        Route::post('/verifications/{user}/reject-seller', function (User $user) {
            $user->update([
                'seller_status'       => 'rejected',
                'seller_rejected_at'  => now(),
                'seller_otp_verified' => false,
            ]);
            if ($user->store) {
                $user->store->update(['status' => 'rejected']);
            }
            return back()->with('success', "Pengajuan seller {$user->name} ditolak.");
        })->name('admin.verifications.reject-seller');

        // User Management
        Route::get('/users', function (\Illuminate\Http\Request $request) {
            $query = User::with('accessCode')->latest();
            if ($request->has('type') && in_array($request->type, ['buyer', 'seller', 'admin'])) {
                if ($request->type === 'admin') {
                    $query->where('role', 'admin');
                }

                if ($request->type === 'seller') {
                    $query->where('is_seller', true);
                }

                if ($request->type === 'buyer') {
                    $query->where('is_seller', false)->where('role', '!=', 'admin');
                }
            }
            $users = $query->paginate(20)->withQueryString();
            return view('admin.users', compact('users'));
        })->name('admin.users.index');

        Route::post('/users/{user}/verify', function (User $user) {
            if ($user->role === 'admin') {
                return back()->withErrors(['message' => 'Akun admin tidak memerlukan verifikasi.']);
            }

            if ($user->accessCode && $user->accessCode->is_active) {
                return back()->with('success', 'Pengguna sudah terverifikasi.');
            }

            $accessCode = AccessCode::where('is_active', true)->first();

            if (! $accessCode) {
                return back()->withErrors(['message' => 'Tidak ada kode akses aktif untuk verifikasi user.']);
            }

            $user->update([
                'access_code_id' => $accessCode->id,
            ]);

            return back()->with('success', "Pengguna {$user->name} berhasil diverifikasi.");
        })->name('admin.users.verify');

        Route::put('/users/{user}', function (Request $request, User $user) {
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string|unique:users,phone,'.$user->id,
                'is_seller' => 'required|boolean',
            ]);
            $payload = $request->only('name', 'phone', 'is_seller');

            if ((bool) $payload['is_seller']) {
                $payload['seller_status'] = 'approved';
                $payload['seller_approved_at'] = now();
                $payload['seller_rejected_at'] = null;
                $payload['seller_rejection_reason'] = null;
            } else {
                $payload['seller_status'] = 'none';
            }

            if ((bool) $payload['is_seller'] === false && $user->store && $user->store->status === 'active') {
                $user->store->update(['status' => 'pending']);
            }

            $user->update($payload);
            return back()->with('success', 'Pengguna berhasil diupdate.');
        })->name('admin.users.update');

        Route::delete('/users/{user}', function (User $user) {
            if ($user->id === auth()->id()) {
                return back()->withErrors(['message' => 'Tidak bisa menghapus akun sendiri!']);
            }
            $user->delete();
            return back()->with('success', 'Pengguna berhasil dihapus.');
        })->name('admin.users.destroy');

        Route::get('/access-codes', function () {
            $codes = AccessCode::withCount('users')->latest()->paginate(15);
            return view('admin.access-codes', compact('codes'));
        })->name('admin.access-codes.index');

        Route::post('/access-codes', function (Request $request) {
            $validated = $request->validate([
                'code' => ['required', 'string', 'max:100', 'unique:access_codes,code'],
            ]);

            AccessCode::create([
                'code' => strtoupper(trim($validated['code'])),
                'is_active' => true,
            ]);

            return back()->with('success', 'Kode akses baru berhasil dibuat.');
        })->name('admin.access-codes.store');

        Route::patch('/access-codes/{accessCode}/toggle', function (AccessCode $accessCode) {
            $willDeactivate = $accessCode->is_active;

            if ($willDeactivate && AccessCode::where('is_active', true)->count() <= 1) {
                return back()->withErrors(['message' => 'Minimal harus ada satu kode akses aktif.']);
            }

            $accessCode->update([
                'is_active' => ! $accessCode->is_active,
            ]);

            return back()->with('success', $accessCode->is_active
                ? 'Kode akses berhasil diaktifkan.'
                : 'Kode akses berhasil dinonaktifkan.');
        })->name('admin.access-codes.toggle');

        Route::delete('/access-codes/{accessCode}', function (AccessCode $accessCode) {
            if ($accessCode->users()->exists()) {
                return back()->withErrors(['message' => 'Kode akses masih dipakai oleh user dan tidak dapat dihapus.']);
            }

            if ($accessCode->is_active && AccessCode::where('is_active', true)->count() <= 1) {
                return back()->withErrors(['message' => 'Kode aktif terakhir tidak dapat dihapus.']);
            }

            $accessCode->delete();

            return back()->with('success', 'Kode akses berhasil dihapus.');
        })->name('admin.access-codes.destroy');
    });

    // Chat routes (buyer and seller)
    Route::middleware(['auth'])->group(function () {
        Route::get('/chat/{order}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{chat}', [ChatController::class, 'store'])->name('chat.store');
        Route::get('/chat/unread/count', [ChatController::class, 'unreadCount'])->name('chat.unread.count');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // WEB CRUD HANDLERS
    // ─────────────────────────────────────────────────────────────────────────
    Route::post('/web/product/store',        [ProductWebController::class, 'store'])->middleware('role:seller');
    Route::put('/web/product/{id}/update',   [ProductWebController::class, 'update'])->middleware('role:seller');
    Route::delete('/web/product/{id}',       [ProductWebController::class, 'destroy'])->middleware('role:seller');

    // Update status order (seller action)
    Route::put('/web/order/{id}/status', function (\Illuminate\Http\Request $request, $id) {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:accepted,rejected,done']);

        $sellerStore = auth()->user()->store;
        if (! $sellerStore || (int) $order->store_id !== (int) $sellerStore->id) {
            abort(403, 'Akses ditolak. Pesanan bukan milik toko Anda.');
        }

        $order->update(['status' => $request->status]);
        
        // Notify buyer
        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderStatusNotification($order));
        }
        
        return redirect('/seller/orders')->with('success', 'Status pesanan berhasil diperbarui.');
    })->middleware('role:seller');

    // Cancel order (buyer action)
    Route::put('/web/order/{order}/cancel', function (Order $order) {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($order->status !== 'pending') {
            return back()->withErrors(['message' => 'Pesanan tidak bisa dibatalkan karena sudah diproses.']);
        }

        $order->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    })->name('buyer.orders.cancel');
});

Route::middleware('auth')->group(function () {
    // Logout tersedia untuk semua user yang sudah auth
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});

