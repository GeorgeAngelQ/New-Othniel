<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/check-token', function (Request $request) {
        return response()->json([
            'authenticated' => true,
            'user' => $request->user()
        ]);
    });
    Route::prefix('v1')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('carts', CartController::class);
        Route::delete('carts/clear', [CartController::class, 'clear']);
        Route::apiResource('orders', OrderController::class);
    });
});
