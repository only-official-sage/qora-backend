<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishController extends Controller
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

        $category = DishCategory::where('id', $category_id)
            ->where('admin_id', auth()->id())
            ->firstOrFail();

        $dish = Dish::create([
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
            "message" => "dish created successfully",
            "data" => $dish
        ], 201);
    }

    public function index()
    {
        $dishes = Dish::with('category')
            ->where('admin_id', auth()->id())
            ->get()
            ->groupBy(function ($dish) {
                return $dish->category->name;
            })
            ->map(function ($group) {
                return [
                    'category_name' => $group->first()->category->name,
                    'data' => $group->take(5)->map(function ($dish) {
                        return [
                            'name' => $dish->name,
                            'price' => $dish->price,
                            'category' => $dish->category->name,
                            'description' => $dish->description,
                            'ingredients' => is_array($dish->ingredients) ? implode(', ', $dish->ingredients) : $dish->ingredients,
                            'image' => $dish->image,
                        ];
                    })->values(),
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => $dishes,
        ]);
    }
}
