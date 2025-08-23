<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;
use Illuminate\Http\Request;

//Route::middleware('api')->group(function () {
//    Route::post('/goods', [GoodController::class, 'store']);
//    Route::get('/goods', [GoodController::class, 'index']);
//});

Route::apiResource('goods', GoodController::class);
