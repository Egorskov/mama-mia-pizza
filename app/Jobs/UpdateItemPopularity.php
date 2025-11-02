<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Models\MenuItem;


class UpdateItemPopularity implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $itemId;
    public function __construct(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $key = "menu:item:{$this->itemId}:popularity";
        Cache::add($key, 0);
        $views = Cache::increment($key); // считаем просмотры

        // обновим рейтинг в базе каждые 20 просмотров
        if ($views % 20 === 0) {
            $item = MenuItem::find($this->itemId);
            if ($item) {
                $item->popularity = $views;
                $item->save();
            }
        }

        // динамическое продление TTL кэша для "горячих" пицц
        $ttl = now()->addMinutes(min(60, 5 + intdiv($views, 10)));
        $cacheKey = "menu:item:{$this->itemId}";
        if ($cached = Cache::get($cacheKey)) {
            Cache::put($cacheKey, $cached, $ttl);
        }
    }
}
