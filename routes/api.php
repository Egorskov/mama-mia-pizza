<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;
use Illuminate\Http\Request;

//Route::middleware('api')->group(function () {
//    Route::post('/goods', [GoodController::class, 'store']);
//    Route::get('/goods', [GoodController::class, 'index']);
//});

Route::apiResource('goods', GoodController::class);

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
