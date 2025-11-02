<?php

namespace App\Http\Controllers;

use App\Exceptions\SomeThingWentWrongException;
use App\Http\Requests\CreateGoodRequest;
use App\Models\Good;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class GoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only([
            'store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $goods = Good::paginate(10);
        return response()->json($goods);
    }

    public function store(CreateGoodRequest $request): JsonResponse
    {
        return response()->json(Good::createGood($request->validated()), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Good $good): JsonResponse
    {
        $redisKey = "views:good:{$good->id}";
        $userKey = "viewed:{$good->id}:" . request()->ip();
        if (!Redis::exists($userKey)) {
            Redis::setex($userKey, 3600, true);
            $newViewsCount = Redis::incr($redisKey);
        } else {
            $newViewsCount = Redis::get($redisKey);
        }
        $good->setAttribute('views_count', (int) $newViewsCount);
        $good->makeVisible('views_count');
        return response()->json($good);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Good $good): JsonResponse
    {
        $data = Good::validateUpdateGood($request);
        $good -> updateGood($data);
        return response()->json($good, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Good $good): JsonResponse
    {
        $good->deleteGood();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
