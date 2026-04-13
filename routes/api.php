<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Public Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public product browsing (no auth required)
Route::get('/products',        [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/updates', [ProductController::class, 'updates']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// ─────────────────────────────────────────────────────────────────────────────
// Protected Routes — require auth:sanctum
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'arradea.access'])->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/profile',  [AuthController::class, 'profile']);
    Route::patch('/profile/seller-mode', [AuthController::class, 'toggleSellerMode']);
    
    // Notifications
    Route::get('/notifications', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->notifications()->take(20)->get()
        ]);
    });
    
    Route::post('/notifications/mark-read', function (\Illuminate\Http\Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    });

    // ─── Seller Routes ────────────────────────────────────────────────────────
    Route::middleware('role:seller')->group(function () {

        // Store management
        Route::get('/store',  [StoreController::class, 'show']);
        Route::post('/store', [StoreController::class, 'store']);
        Route::put('/store',  [StoreController::class, 'update']);

        // Product management
        Route::post('/products',               [ProductController::class, 'store']);
        Route::put('/products/{product}',      [ProductController::class, 'update']);
        Route::delete('/products/{product}',   [ProductController::class, 'destroy']);

        // Order management
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    });

    // ─── Buyer Routes ─────────────────────────────────────────────────────────
    Route::middleware([])->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });

    // ─── Shared Order Routes (buyer sees own, seller sees store's) ───────────
    Route::middleware([])->group(function () {
        Route::get('/orders',        [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });

    // ─── Admin Routes ─────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/sellers',             [AdminController::class, 'sellers']);
        Route::put('/sellers/{user}',      [AdminController::class, 'updateSeller']);
        Route::delete('/sellers/{user}',   [AdminController::class, 'destroySeller']);
    });
});
