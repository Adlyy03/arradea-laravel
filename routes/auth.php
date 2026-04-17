<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// Kirim ulang link verifikasi
Route::post('/phone/verification-notification', [PhoneVerificationController::class, 'send'])
    // ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.phone.send');

// Link yang diklik user dari WA (tidak perlu auth, pakai signed URL)
Route::get('/phone/verify/{id}/{hash}', [PhoneVerificationController::class, 'verify'])
    // ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.phone.verify');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/admin/sellers', function () {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return \App\Models\User::where('is_seller', true)->get();
    });

});