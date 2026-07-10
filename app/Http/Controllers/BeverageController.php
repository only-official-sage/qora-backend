<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use App\Models\BeverageCategory;
use Illuminate\Http\Request;

class BeverageController extends Controller
{
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
            ->where('admin_id', auth()->id())
            ->firstOrFail();

        $beverage = Beverage::create([
            'admin_id' => auth()->id(),
            'category_id' => $category->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'ingredients' => $validated['ingredients'] ? array_map('trim', explode(',', $validated['ingredients'])) : [],
            'image' => $validated['image'],
        ]);

        return response()->json([
            "success" => true,
            "message" => "beverage created successfully",
            "data" => $beverage
        ], 201);
    }

    public function index()
    {
        $beverages = Beverage::with('category')
            ->where('admin_id', auth()->id())
            ->get()
            ->groupBy(fn($item) => $item->category->name)
            ->map(fn($group) => $group->take(5))
            ->map(fn($group) => $group->map(fn($item) => [
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
            ]));
        return response()->json($beverages);
    }
}
