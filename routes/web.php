<?php

use App\Http\Controllers\TestQueueController;
use App\Models\Good;
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
Route::get('/cache-debug', function () {
    $results = [];

    // Тест получения всех товаров
    $start = microtime(true);
    $goods = Good::getAllGoods();
    $timeFirst = microtime(true) - $start;

    $start = microtime(true);
    $goods = Good::getAllGoods();
    $timeSecond = microtime(true) - $start;

    $results['getAllGoods'] = [
        'first_call_ms' => round($timeFirst * 1000, 2),
        'second_call_ms' => round($timeSecond * 1000, 2),
        'is_cached' => $timeSecond < $timeFirst / 3
    ];

    // Тест получения одного товара
    if ($firstGood = Good::first()) {
        $start = microtime(true);
        $good = Good::findOrFail($firstGood->id);
        $timeFirst = microtime(true) - $start;

        $start = microtime(true);
        $good = Good::findOrFail($firstGood->id);
        $timeSecond = microtime(true) - $start;

        $results['findOrFail'] = [
            'first_call_ms' => round($timeFirst * 1000, 2),
            'second_call_ms' => round($timeSecond * 1000, 2),
            'is_cached' => $timeSecond < $timeFirst / 3
        ];
    }

    // Проверка ключей в Redis
    try {
        $redis = app('redis')->connection();
        $keys = $redis->keys('goods:*');
        $results['redis_keys'] = $keys;
        $results['redis_keys_count'] = count($keys);
    } catch (\Exception $e) {
        $results['redis_error'] = $e->getMessage();
    }

    return response()->json($results);
});
// routes/web.php
Route::get('/redis-check', function() {
    $results = [];

    // 1. Проверка кэша
    Cache::put('http_test', 'http_value', 60);
    $results['cache_test'] = Cache::get('http_test');

    // 2. Проверка прямого подключения
    $results['direct_connection'] = [
        'ping' => app('redis')->ping(),
        'keys_in_default_db' => app('redis')->keys('*')
    ];

    // 3. Проверка через хранилище кэша
    $results['cache_store'] = [
        'class' => get_class(Cache::getStore()),
        'keys' => Cache::getStore()->getRedis()->keys('*')
    ];

    // 4. Проверка конфигурации
    $results['config'] = [
        'cache_driver' => config('cache.default'),
        'redis_database' => config('database.redis.default.database'),
        'redis_host' => config('database.redis.default.host')
    ];

    return response()->json($results);
});
Route::get('/test-queue', [TestQueueController::class, 'testQueue']);
Route::get('/test-sync', [TestQueueController::class, 'testSync']);
// routes/web.php
Route::get('/redis-direct', function() {
    // Получите все ключи
    $keys = app('redis')->keys('*');

    $results = [];
    foreach ($keys as $key) {
        $results[$key] = [
            'exists' => app('redis')->exists($key),
            'type' => app('redis')->type($key),
            'ttl' => app('redis')->ttl($key),
            'value' => app('redis')->get($key),
            'dbsize' => app('redis')->dbsize()
        ];
    }

    return response()->json($results);
});

Route::get('/test-rabbit', function () {
    \App\Jobs\TestRabbitJob::dispatch('Hello from RabbiMQ!');
    return '✅ Job dispatched to RabbitMQ!';
});


