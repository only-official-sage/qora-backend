<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishCategoryController extends Controller
{
    // public function index()
    public function index(Request $request)
    {
        $categories = DishCategory::where('admin_id', $request->user()->getKey())->get();

        return response()->json($categories);
    }

    public function show(Request $request, $id)
    {
        $category = DishCategory::where('admin_id', $request->user()->getKey())->findOrFail($id);

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = DishCategory::create([
            'admin_id' => $request->user()->getKey(),
            'name' => $validated['name'],
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = DishCategory::where('admin_id', $request->user()->getKey())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $category = DishCategory::where('admin_id', $request->user()->getKey())->findOrFail($id);

        if ($category->dishes()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with dishes'], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}

