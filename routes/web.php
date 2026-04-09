<?php

use App\Http\Controllers\AuthWebController;
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
});

// ─────────────────────────────────────────────────────────────────────────────
// PROTECTED DASHBOARDS (Session-Based Auth)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

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

    Route::middleware(['role:buyer'])->group(function () {
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
                'seller_status' => 'pending',
                'seller_applied_at' => now(),
                'seller_rejected_at' => null,
                'seller_rejection_reason' => null,
            ]);

            $storeData = [
                'name' => $request->store_name,
                'description' => $request->store_description,
                'address' => $request->store_address,
                'status' => 'pending',
            ];

            if ($user->store) {
                $user->store->update($storeData);
            } else {
                $user->store()->create($storeData);
            }

            return redirect()->route('profile')->with('success', 'Permohonan seller berhasil dikirim. Tunggu persetujuan admin.');
        })->name('seller.apply.store');
    });

    // 🛒 BUYER
    Route::middleware('role:buyer')->prefix('buyer')->group(function () {
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
        
        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::all();
        
        return view('buyer.products.index', compact('products', 'categories'));
    })->name('buyer.products');

    Route::get('/products/{id}', function ($id) {
        $product = Product::with('store')->findOrFail($id);
        return view('buyer.products.show', compact('product'));
    })->name('buyer.products.show');

    // Web order submission (buyer only)
    Route::post('/web/order', [OrderController::class, 'store'])->middleware('role:buyer')->name('web.order.store');

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
                'role' => 'seller',
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
            if ($request->has('role') && in_array($request->role, ['buyer', 'seller', 'admin'])) {
                $query->where('role', $request->role);
            }
            $users = $query->paginate(20)->withQueryString();
            return view('admin.users', compact('users'));
        })->name('admin.users.index');

        Route::put('/users/{user}', function (Request $request, User $user) {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'role' => 'required|in:admin,seller,buyer',
            ]);
            $user->update($request->only('name', 'email', 'role'));
            return back()->with('success', 'Pengguna berhasil diupdate.');
        })->name('admin.users.update');

        Route::delete('/users/{user}', function (User $user) {
            if ($user->id === auth()->id()) {
                return back()->withErrors(['message' => 'Tidak bisa menghapus akun sendiri!']);
            }
            $user->delete();
            return back()->with('success', 'Pengguna berhasil dihapus.');
        })->name('admin.users.destroy');
    });

    // Chat routes (buyer and seller)
    Route::middleware(['auth', 'role:buyer,seller'])->group(function () {
        Route::get('/chat/{order}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{chat}', [ChatController::class, 'store'])->name('chat.store');
        Route::get('/chat/unread/count', [ChatController::class, 'unreadCount'])->name('chat.unread.count');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // WEB CRUD HANDLERS
    // ─────────────────────────────────────────────────────────────────────────
    Route::post('/web/product/store',        [ProductWebController::class, 'store']);
    Route::put('/web/product/{id}/update',   [ProductWebController::class, 'update']);
    Route::delete('/web/product/{id}',       [ProductWebController::class, 'destroy']);

    // Update status order (seller action)
    Route::put('/web/order/{id}/status', function (\Illuminate\Http\Request $request, $id) {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:accepted,rejected,done']);
        $order->update(['status' => $request->status]);
        
        // Notify buyer
        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderStatusNotification($order));
        }
        
        return redirect('/seller/orders')->with('success', 'Status pesanan berhasil diperbarui.');
    })->middleware('role:seller');

    // LOGOUT
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});
