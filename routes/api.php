<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartDetailController;
use App\Http\Controllers\PaymentController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn(Request $request) => $request->user());
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/check-token', fn(Request $request) => response()->json([
            'authenticated' => true,
            'user' => $request->user()
        ]));
        Route::get('/cart/current', [CartController::class, 'getOrCreateCart']);
        Route::middleware('role:admin')->group(function () {

            Route::apiResource('users', UserController::class);
        });

        Route::middleware('role:manager')->group(function () {
            Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'update']);
        });

        Route::middleware('role:user')->group(function () {
            Route::apiResource('products', ProductController::class)->except(['store','update','destroy']);
            Route::apiResource('carts', CartController::class);
            Route::delete('carts/clear', [CartController::class, 'clear']);
            Route::apiResource('cart-details', CartDetailController::class)->except(['show', 'edit', 'create']);
            Route::apiResource('orders', OrderController::class);
            Route::get('/payments/mercadopago/{id_cart}', [PaymentController::class, 'createPreferenceMercadoPago']);
        });
    });
});
