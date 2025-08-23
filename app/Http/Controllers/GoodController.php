<?php

namespace App\Http\Controllers;

use App\Exceptions\SomeThingWentWrongException;
use App\Http\Requests\CreateGoodRequest;
use App\Models\Good;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goods = Good::all();
        return response()->json($goods);
    }

    public function store(CreateGoodRequest $request)
    {
        return response()->json(Good::create($request->validated([])), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Good::findOrFail($id));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $good = Good::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|min:0',
            'weight' => 'required|integer|min:0',
            'category' => 'required|in:pizza,drink',
        ]);

        $good -> update($data);
        return response()->json($good, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $good = Good::findOrFail($id);
        $good->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}
