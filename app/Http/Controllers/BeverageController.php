<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use App\Models\BeverageCategory;
use Illuminate\Http\Request;

class BeverageController extends Controller
{
    public function index(Request $request)
    {
        $query = Beverage::with('category')->where('admin_id', $request->user()->getKey());

        if ($request->query('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $beverages = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'description' => $item->description,
                'ingredients' => is_array($item->ingredients) ? implode(', ', $item->ingredients) : $item->ingredients,
                'image' => $item->image,
                'category' => [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                ],
            ];
        });

        return response()->json($beverages);
    }

    public function show(Request $request, $id)
    {
        $beverage = Beverage::where('admin_id', $request->user()->getKey())->with('category')->findOrFail($id);

        return response()->json([
            'id' => $beverage->id,
            'name' => $beverage->name,
            'price' => $beverage->price,
            'description' => $beverage->description,
            'ingredients' => is_array($beverage->ingredients) ? implode(', ', $beverage->ingredients) : $beverage->ingredients,
            'image' => $beverage->image,
            'category' => [
                'id' => $beverage->category->id,
                'name' => $beverage->category->name,
            ],
        ]);
    }

    public function store(Request $request, $category_id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $category = BeverageCategory::where('id', $category_id)
            ->where('admin_id', $request->user()->getKey())
            ->firstOrFail();

        $beverage = Beverage::create([
            'admin_id' => $request->user()->getKey(),
            'category_id' => $category->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'ingredients' => $validated['ingredients'] ? array_map('trim', explode(',', $validated['ingredients'])) : [],
            'image' => $validated['image'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'beverage created successfully',
            'data' => $beverage,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $beverage = Beverage::where('admin_id', $request->user()->getKey())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        if (isset($validated['ingredients']) && is_string($validated['ingredients'])) {
            $validated['ingredients'] = array_map('trim', explode(',', $validated['ingredients']));
        }

        $beverage->update($validated);

        return response()->json($beverage);
    }

    public function destroy(Request $request, $id)
    {
        $beverage = Beverage::where('admin_id', $request->user()->getKey())->findOrFail($id);
        $beverage->delete();

        return response()->json(null, 204);
    }
}

