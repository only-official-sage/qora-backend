<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishController extends Controller
{
    public function index(Request $request)
    {
        $query = Dish::with('category')->where('admin_id', auth()->id());

        if ($request->query('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->query('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->query('search').'%')
                    ->orWhere('description', 'like', '%'.$request->query('search').'%');
            });
        }

        $dishes = $query->get()->map(function ($dish) {
            return [
                'id' => $dish->id,
                'name' => $dish->name,
                'price' => $dish->price,
                'description' => $dish->description,
                'ingredients' => is_array($dish->ingredients) ? implode(', ', $dish->ingredients) : $dish->ingredients,
                'image' => $dish->image,
                'category' => [
                    'id' => $dish->category->id,
                    'name' => $dish->category->name,
                ],
            ];
        });

        return response()->json($dishes);
    }

    public function show($id)
    {
        $dish = Dish::where('admin_id', auth()->id())->with('category')->findOrFail($id);

        return response()->json([
            'id' => $dish->id,
            'name' => $dish->name,
            'price' => $dish->price,
            'description' => $dish->description,
            'ingredients' => is_array($dish->ingredients) ? implode(', ', $dish->ingredients) : $dish->ingredients,
            'image' => $dish->image,
            'category' => [
                'id' => $dish->category->id,
                'name' => $dish->category->name,
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
            'success' => true,
            'message' => 'dish created successfully',
            'data' => $dish,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $dish = Dish::where('admin_id', auth()->id())->findOrFail($id);

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

        $dish->update($validated);

        return response()->json($dish);
    }

    public function destroy($id)
    {
        $dish = Dish::where('admin_id', auth()->id())->findOrFail($id);
        $dish->delete();

        return response()->json(null, 204);
    }
}
