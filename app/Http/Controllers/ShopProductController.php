<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use Illuminate\Http\Request;

class ShopProductController extends Controller
{
    public function index()
    {
        $products = ShopProduct::where('admin_id', auth()->id())->get();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = ShopProduct::where('admin_id', auth()->id())->findOrFail($id);

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product = ShopProduct::create(array_merge($validated, [
            'admin_id' => auth()->id(),
            'stock' => $validated['stock'] ?? 0,
        ]));

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = ShopProduct::where('admin_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|string',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = ShopProduct::where('admin_id', auth()->id())->findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
