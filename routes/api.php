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

// Public test notification endpoint (no auth required)
Route::post('/test-notification-public', function () {
    $pushService = app(\App\Services\PushNotificationService::class);
    
    // Get first user with active token
    $token = \App\Models\FcmToken::active()->with('user')->first();
    
    if (!$token) {
        return response()->json([
            'success' => false,
            'message' => 'No active FCM tokens found'
        ]);
    }
    
    $result = $pushService->sendToUser(
        $token->user,
        '🎉 Test Notification',
        'This is a test notification from the web interface!',
        [
            'type' => 'web_test',
            'timestamp' => now()->toIso8601String()
        ],
        asset('icons/logo-arradea.png'),
        url('/')
    );
    
    return response()->json($result);
});

// ─────────────────────────────────────────────────────────────────────────────
// Protected Routes — require auth:sanctum
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'arradea.access'])->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/profile',  [AuthController::class, 'profile']);
    Route::patch('/profile/seller-mode', [AuthController::class, 'toggleSellerMode']);
    
    // ─── Mode Switching (Buyer ⇄ Seller) ─────────────────────────────────────
    Route::post('/mode/switch', [\App\Http\Controllers\ModeController::class, 'switch']);
    Route::get('/mode/info', [\App\Http\Controllers\ModeController::class, 'info']);
    
    // Notifications
    Route::get('/notifications', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->notifications()->take(20)->get()
        ]);
    });
    
    Route::post('/notifications/mark-read', function (\Illuminate\Http\Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai telah dibaca']);
    });
    
    // Test notification endpoint (authenticated)
    Route::post('/test-notification', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $pushService = app(\App\Services\PushNotificationService::class);
        
        $result = $pushService->sendToUser(
            $user,
            '🧪 API Test Notification',
            'This is a test notification from API endpoint',
            [
                'type' => 'api_test',
                'timestamp' => now()->toIso8601String()
            ],
            asset('icons/logo-arradea.png'),
            url('/')
        );
        
        return response()->json($result);
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
        Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggleActive']);

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
