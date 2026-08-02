<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\WishlistApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ───────────────────────────────────────────────────────────

// Sản phẩm (công khai, không cần đăng nhập)
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{slug}', [ProductApiController::class, 'show']);

// ─── AUTH SYNC (Firebase → Laravel) ─────────────────────────────────────────
Route::post('/auth/sync', [AuthController::class, 'sync']);

// ─── AUTHENTICATED ROUTES (Firebase Bearer Token) ────────────────────────────
Route::middleware('auth.firebase')->group(function () {

    // Đơn hàng
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);

    // Wishlist
    Route::get('/wishlist', [WishlistApiController::class, 'index']);
    Route::post('/wishlist', [WishlistApiController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistApiController::class, 'destroy']);
});
