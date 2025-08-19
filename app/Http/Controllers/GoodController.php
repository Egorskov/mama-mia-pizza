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
        return view('goods.index', compact('goods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('goods.create');
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
        Good::create($data);
        return redirect()->route('goods.index')
            ->with('success', 'Good created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $good = Good::findOrFail($id);
        return view('goods.show', compact('good'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $good = Good::findOrFail($id);
        return view('goods.edit', compact('good'));
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
        return redirect()->route('goods.index')
            ->with('success', 'Good created successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $good = Good::findOrFail($id);
        $good -> delete();
        return redirect()->route('goods.index')
            ->with('success', 'Good deleted successfully.');
    }
}
