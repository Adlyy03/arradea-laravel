<?php

use Illuminate\Support\Facades\Route;

// TEMPORARY DEBUG ROUTE - DELETE AFTER DEBUGGING
Route::get('/debug-mode', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json(['error' => 'Tidak terautentikasi']);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'name' => $user->name,
        'is_seller' => $user->is_seller,
        'seller_status' => $user->seller_status,
        'preferred_mode' => $user->preferred_mode ?? 'NOT SET',
        'session_active_mode' => session('active_mode', 'NOT SET'),
        'canSwitchToSellerMode' => $user->canSwitchToSellerMode(),
        'getActiveMode' => $user->getActiveMode(),
    ]);
})->middleware('auth');
