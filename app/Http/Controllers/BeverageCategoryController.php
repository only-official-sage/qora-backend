<?php

namespace App\Http\Controllers;

use App\Models\BeverageCategory;
use Illuminate\Http\Request;

class BeverageCategoryController extends Controller
{
    public function index()
    {
        $categories = BeverageCategory::where('admin_id', auth()->id())->get();

        return response()->json($categories);
    }

    public function show($id)
    {
        $category = BeverageCategory::where('admin_id', auth()->id())->findOrFail($id);

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = BeverageCategory::create([
            'admin_id' => auth()->id(),
            'name' => $validated['name'],
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = BeverageCategory::where('admin_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = BeverageCategory::where('admin_id', auth()->id())->findOrFail($id);

        if ($category->beverages()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with beverages'], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
