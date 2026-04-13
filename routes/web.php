<?php

use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\AccessCode;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Arradea Marketplace — Web Routes (Final Production-Ready Interface)
// ─────────────────────────────────────────────────────────────────────────────

// PUBLIC & GUEST
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',    fn() => view('auth.login'))->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    
    // Web Auth Handlers
    Route::post('/web/login',    [AuthWebController::class, 'login']);
    Route::post('/web/register', [AuthWebController::class, 'register']);
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ─────────────────────────────────────────────────────────────────────────────
// PROTECTED DASHBOARDS (Session-Based Auth)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'arradea.access'])->group(function () {

    // 👨💼 ADMIN
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'));
        Route::get('/sellers',   fn() => view('admin.sellers'));
    });

    // 🏪 SELLER
    Route::middleware('role:seller')->prefix('seller')->group(function () {
        Route::get('/dashboard', fn() => view('seller.dashboard'))->name('seller.dashboard');
        
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

        return view('seller.apply', [
            'user' => $user,
        ]);
    })->name('seller.apply');

    Route::post('/seller/apply', function (Request $request) {
        $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $user->update([
            'is_seller' => true,
            'seller_status' => 'approved',
            'seller_applied_at' => now(),
            'seller_approved_at' => now(),
            'seller_rejected_at' => null,
            'seller_rejection_reason' => null,
        ]);

        $storeData = [
            'name' => $request->store_name,
            'description' => $request->store_description,
            'address' => $request->store_address,
            'status' => 'active',
            'approved_at' => now(),
        ];

        if ($user->store) {
            $user->store->update($storeData);
        } else {
            $user->store()->create($storeData);
        }

        return redirect()->route('seller.dashboard')->with('success', 'Mode seller berhasil diaktifkan. Anda sekarang bisa jualan sekaligus belanja.');
    })->name('seller.apply.store');

    Route::post('/seller/activate', function (Request $request) {
        $request->validate([
            'store_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        if (! $user->is_seller) {
            $user->update([
                'is_seller' => true,
                'seller_status' => 'approved',
                'seller_applied_at' => now(),
                'seller_approved_at' => now(),
                'seller_rejected_at' => null,
                'seller_rejection_reason' => null,
            ]);
        }

        if (! $user->store) {
            $user->store()->create([
                'name' => $request->store_name ?: 'Toko ' . $user->name,
                'description' => 'Selamat datang di toko kami.',
                'status' => 'active',
                'approved_at' => now(),
            ]);
        }

        return back()->with('success', 'Mode seller aktif. Anda sekarang dapat mengakses fitur jual.');
    })->name('seller.activate');

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
        $query = Product::with('store', 'category')->latest();
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
        $product = Product::with('store')->findOrFail($id);
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
        $products = $category->products()->with('store')->paginate(20);
        return view('categories.show', compact('category', 'products'));
    })->name('categories.show');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::middleware('role:admin')->prefix('admin')->group(function () {
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

        // User Management
        Route::get('/users', function (\Illuminate\Http\Request $request) {
            $query = User::latest();
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

        Route::put('/users/{user}', function (Request $request, User $user) {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'is_seller' => 'required|boolean',
            ]);
            $payload = $request->only('name', 'email', 'is_seller');

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

    // LOGOUT
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});
