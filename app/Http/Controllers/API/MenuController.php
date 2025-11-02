<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateItemPopularity;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Menu",
 *   description="Работа с меню"
 * )
 */
class MenuController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/ping",
     *   tags={"System"},
     *   summary="Ping API",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="ok")
     *     )
     *   )
     * )
     */
    public function ping(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/menu/naive",
     *   tags={"Menu"},
     *   summary="Наивная выборка меню (без кэша)",
     *   @OA\Parameter(
     *     name="category",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string", example="pizza")
     *   ),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function naive(Request $r): JsonResponse
    {
        $rows = DB::table('menu_items')
            ->when($r->category, fn ($q) => $q->where('category', $r->category))
            ->limit(100)
            ->get();

        return response()->json($rows);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/menu",
     *   tags={"Menu"},
     *   summary="Фильтр меню с кэшированием",
     *   @OA\Parameter(
     *     name="category",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string", example="pizza")
     *   ),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function filter(Request $r): JsonResponse
    {
        $key = 'menu:' . md5(json_encode($r->query(), JSON_UNESCAPED_UNICODE));

        $rows = Cache::remember($key, now()->addSeconds(120), function () use ($r) {
            return DB::table('menu_items')
                ->when($r->category, fn ($q) => $q->where('category', $r->category))
                ->limit(100)
                ->get();
        });

        return response()->json($rows);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/items/{id}",
     *   tags={"Menu"},
     *   summary="Карточка позиции меню",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer", example=10)
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);

        UpdateItemPopularity::dispatch($id)->onQueue('analytics');

        return response()->json($item);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/menu/all",
     *   tags={"Menu"},
     *   summary="Получить всё меню",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function getMenu(): JsonResponse
    {
        return response()->json(
            MenuItem::query()->get(['id', 'name', 'category', 'price'])
        );
    }
}
