<?php

namespace App\Http\Controllers;

use App\Models\BeverageCategory;
use Illuminate\Http\Request;

class BeverageCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $beverageCategory = BeverageCategory::create([
            'name' => $validated['name'],
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            "success" => true,
            "message" => "beverage category created successfully",
            "data" => $beverageCategory
        ], 201);
    }
}
