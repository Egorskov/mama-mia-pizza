<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return ['data'=>'hello world'];
});

Route::get('/healthcheck', function () {
    return response()->json(['status' => 'ok']);
});

Route::resource('goods', GoodController::class);
