<?php
declare(strict_types=1);
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PizzaOfferController;
use App\Http\Controllers\PizzaSseController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\MenuController;

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
Route::get('/internal/orders/{order}', function (Order $order) {
    $order->load(['items.good', 'user']);

    return response()->json([
        'id' => $order->id,
        'user_email' => $order->user?->email,
        'total_price' => $order->items->sum('total_price'), // 👈 сумма заказа
        'items' => $order->items->map(fn($item) => [
            'good_name' => $item->good?->name ?? 'Товар',
            'quantity' => $item->quantity,
            'total_price' => $item->total_price,
        ]),
    ]);
});
Route::get('/ping', [MenuController::class, 'ping']);
Route::get('/v1/menu/all', [MenuController::class, 'getMenu']);
Route::get('/v1/menu/naive', [MenuController::class, 'naive']);
Route::get('/v1/menu', [MenuController::class, 'filter']);
Route::get('/v1/items/{id}', [MenuController::class, 'show']);
Route::get('/v1/pizzas/{id}/offers', [PizzaOfferController::class, 'show']);
Route::get('/v1/pizzas/stream', [PizzaSseController::class, 'stream']);
