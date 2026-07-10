<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function index()
    {
        return response()->json(Section::where('admin_id', auth()->id())->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'icon' => 'required|array',
        ]);

        $section = Section::create(array_merge($validated, [
            'admin_id' => $request->user()->id,
        ]));

        return response()->json($section, 201);
    }
}
