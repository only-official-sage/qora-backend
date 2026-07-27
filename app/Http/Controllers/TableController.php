<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request, $sectionId = null)
    {
        $adminId = auth()->id();
        $query = Table::where('admin_id', $adminId);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $tables = $query->with('section')->get();

        return response()->json($tables);
    }

    public function show($id)
    {
        $table = Table::where('admin_id', auth()->id())->with('section')->findOrFail($id);

        return response()->json($table);
    }

    public function store(Request $request, $sectionId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'sometimes|in:available,occupied,reserved',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $section = Section::where('admin_id', auth()->id())->findOrFail($sectionId);

        $table = Table::create([
            'admin_id' => auth()->id(),
            'section_id' => $section->id,
            'name' => $validated['name'],
            'status' => $validated['status'] ?? 'available',
            'capacity' => $validated['capacity'] ?? null,
        ]);

        return response()->json($table, 201);
    }

    public function update(Request $request, $sectionId, $tableId)
    {
        $table = Table::where('admin_id', auth()->id())
            ->where('section_id', $sectionId)
            ->findOrFail($tableId);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:available,occupied,reserved',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $table->update($validated);

        return response()->json($table);
    }

    public function destroy($sectionId, $tableId)
    {
        $table = Table::where('admin_id', auth()->id())
            ->where('section_id', $sectionId)
            ->findOrFail($tableId);

        $table->delete();

        return response()->json(null, 204);
    }

    public function toggleStatus($id)
    {
        $table = Table::where('admin_id', auth()->id())->findOrFail($id);

        $statuses = ['available', 'occupied', 'reserved'];
        $currentIndex = array_search($table->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $table->status = $statuses[$nextIndex];
        $table->save();

        return response()->json($table);
    }
}
