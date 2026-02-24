<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Random\RandomException;
use App\Jobs\UpdateItemPopularity;

class MenuItemsSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $categories = ['pizza','drinks','snacks','sauces'];
        $sizes = [25,30,35,null];

        $batch = [];
        for ($i=0; $i<200000; $i++) {
            $cat = $categories[array_rand($categories)];
            $size = $sizes[array_rand($sizes)];
            $batch[] = [
                'name'       => Str::title($cat).' #'.Str::random(6),
                'category'   => $cat,
                'size'       => $size,
                'popularity' => random_int(0, 5000),
                'price'      => random_int(199, 1599),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($batch) >= 2000) {
                DB::table('menu_items')->insert($batch);
                $batch = [];
            }
        }
        if ($batch) DB::table('menu_items')->insert($batch);
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
