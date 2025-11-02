<?php

namespace App\Http\Controllers;

use App\Exceptions\SomeThingWentWrongException;
use App\Http\Requests\CreateGoodRequest;
use App\Models\Good;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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
        $goods = Good::getAllGoods();
        return response()->json($goods);
    }

    public function store(CreateGoodRequest $request): JsonResponse
    {
        return response()->json(Good::createGood($request->validated()), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json(Good::findOrFail($id));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $good = Good::findOrFail($id);
        $data = Good::validateUpdateGood($request);
        $good -> updateGood($data);
        return response()->json($good, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $good = Good::findOrFail($id);
        $good->deleteGood();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }

}
