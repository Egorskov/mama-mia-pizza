<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|min:0',
            'weight' => 'required|integer|min:0',
            'category' => 'required|in:pizza,drink',
        ]);

        $good = Good::create($data);

        return response()->json($good, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $good = Good::findOrFail($id);
        return response()->json($good);
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
        return response()->json($good, 'Good created successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $good = Good::findOrFail($id);
        $good->delete();
        return response()->json(null, 'deleted');
    }
}
