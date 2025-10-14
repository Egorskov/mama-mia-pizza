<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;

Route::apiResource('goods', GoodController::class);
