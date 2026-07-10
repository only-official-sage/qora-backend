<?php

namespace App\Http\Controllers;

use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $dishCategory = DishCategory::create([
            'name' => $validated['name'],
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            "success" => true,
            "message" => "dish category created successfully",
            "data" => $dishCategory
        ], 201);
    }
} 