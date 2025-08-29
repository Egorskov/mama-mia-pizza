<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;
use Illuminate\Http\Request;


Route::get('goods', [GoodController::class, 'index']);
Route::get('goods/{id}', [GoodController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('goods', [GoodController::class, 'store']);
    Route::put('goods/{id}', [GoodController::class, 'update']);
    Route::delete('goods/{id}', [GoodController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
});

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('orders', [\App\Http\Controllers\AdminController::class, 'adminIndex']);
    Route::get('orders/{order}', [\App\Http\Controllers\AdminController::class, 'adminOrder']);
    Route::get('id/{id}', [\App\Http\Controllers\AdminController::class, 'adminId']);
    Route::patch('update/{order}', [\App\Http\Controllers\AdminController::class, 'adminUpdate']);
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
