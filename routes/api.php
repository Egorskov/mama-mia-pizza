<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;
use Illuminate\Http\Request;

//Route::middleware('api')->group(function () {
//    Route::post('/goods', [GoodController::class, 'store']);
//    Route::get('/goods', [GoodController::class, 'index']);
//});

Route::apiResource('goods', GoodController::class);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
});

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('orders', [OrderController::class, 'adminIndex']);
    Route::get('orders/{order}', [OrderController::class, 'adminOrder']);
    Route::get('id/{id}', [OrderController::class, 'adminId']);
    Route::patch('update/{order}', [OrderController::class, 'adminUpdate']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('auth:api')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
