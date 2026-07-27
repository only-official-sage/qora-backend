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

    public function show($id)
    {
        $section = Section::where('admin_id', auth()->id())->findOrFail($id);

        return response()->json($section);
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

    public function update(Request $request, $id)
    {
        $section = Section::where('admin_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|string',
            'icon' => 'sometimes|array',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $section->update($validated);

        return response()->json($section);
    }

    public function destroy($id)
    {
        $section = Section::where('admin_id', auth()->id())->findOrFail($id);
        $section->delete();

        return response()->json(null, 204);
    }

    public function toggleStatus($id)
    {
        $section = Section::where('admin_id', auth()->id())->findOrFail($id);

        $section->status = $section->status === 'active' ? 'inactive' : 'active';
        $section->save();

        return response()->json($section);
    }
}
