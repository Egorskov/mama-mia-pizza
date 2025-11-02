<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateItemPopularity;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    // Пример: /api/v1/menu/naive?category=pizza&size=30&popular=1
    public function naive(Request $r)
    {
        $start = microtime(true);

        $q = DB::table('menu_items')
            ->when($r->category, fn($q) => $q->where('category', $r->category))
            ->when($r->size,     fn($q) => $q->where('size', $r->size))
            ->when($r->popular,  fn($q) => $q->orderByDesc('popularity'))
            ->limit(100);

        $rows = $q->get();

        return response()->json([
            'rows'   => $rows,
            'timing' => round(microtime(true)-$start, 4) . 's',
            'note'   => 'наивный запрос без индексов/кэша'
        ]);
    }


    public function filter(Request $r)
    {
        $key = 'menu:filter:' . md5(json_encode($r->query()));
        $ttl = now()->addMinutes(2); // скользящий TTL 2 минуты
        $start = microtime(true);

        if ($cached = Cache::get($key)) {
            // продлеваем TTL — «горячие» фильтры живут весь день
            Cache::put($key, $cached, $ttl);
            return response()->json([
                'rows'   => $cached,
                'timing' => round(microtime(true)-$start, 4) . 's',
                'source' => 'redis'
            ]);
        }

        $rows = \DB::table('menu_items')
            ->when($r->category, fn($q) => $q->where('category', $r->category))
            ->when($r->size,     fn($q) => $q->where('size', $r->size))
            ->when($r->popular,  fn($q) => $q->orderByDesc('popularity'))
            ->limit(100)->get();

        Cache::put($key, $rows, $ttl);

        return response()->json([
            'rows'   => $rows,
            'timing' => round(microtime(true)-$start, 4) . 's',
            'source' => 'db'
        ]);
    }
    public function show(Request $r, $id)
    {
        $key = "menu:item:$id";

        // пробуем взять из кэша
        if ($cached = Cache::get($key)) {
            // отправляем в фоне задачу обновления популярности
            UpdateItemPopularity::dispatch($id)->onQueue('analytics');

            return response()->json(['source' => 'redis', 'data' => $cached]);
        }

        // если нет — грузим из БД
        $item = MenuItem::findOrFail($id)->toArray();
        Cache::put($key, $item, now()->addMinutes(10));

        // тоже отправляем задачу в фоне
        UpdateItemPopularity::dispatch($id)->onQueue('analytics');

        return response()->json(['source' => 'db', 'data' => $item]);
    }
}
