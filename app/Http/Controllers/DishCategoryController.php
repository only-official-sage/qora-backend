<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishCategoryController extends Controller
{
    public function index()
    {
        $categories = DishCategory::where('admin_id', auth()->id())->get();

        return response()->json($categories);
    }

    public function show($id)
    {
        $category = DishCategory::where('admin_id', auth()->id())->findOrFail($id);

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = DishCategory::create([
            'admin_id' => auth()->id(),
            'name' => $validated['name'],
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = DishCategory::where('admin_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = DishCategory::where('admin_id', auth()->id())->findOrFail($id);

        if ($category->dishes()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with dishes'], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
