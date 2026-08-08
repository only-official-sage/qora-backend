<?php

namespace App\Http\Controllers;

use App\Models\CustomerRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $adminId = auth()->id();
        $query = CustomerRequest::where('admin_id', $adminId)->with('table');

        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->query('table_id')) {
            $query->where('table_id', $request->query('table_id'));
        }

        $requests = $query->latest()->get();

        return response()->json($requests);
    }

    public function show($id)
    {
        $request = CustomerRequest::where('admin_id', auth()->id())->with('table')->findOrFail($id);

        return response()->json($request);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|string|exists:tables,id',
            'type' => 'required|string',
            'note' => 'nullable|string',
            'payload' => 'nullable|array',
        ]);

        $customerRequest = CustomerRequest::create([
            'admin_id' => auth()->id(),
            'table_id' => $validated['table_id'] ?? null,
            'type' => $validated['type'],
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
            'payload' => $validated['payload'] ?? null,
        ]);

        return response()->json($customerRequest, 201);
    }

    public function publicStore(Request $request, string $admin_id)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|string|exists:tables,id',
            'type' => 'required|string',
            'note' => 'nullable|string',
            'payload' => 'nullable|array',
        ]);

        $customerRequest = CustomerRequest::create([
            'admin_id' => $admin_id,
            'table_id' => $validated['table_id'] ?? null,
            'type' => $validated['type'],
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
            'payload' => $validated['payload'] ?? null,
        ]);

        return response()->json($customerRequest, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,handled,cancelled',
        ]);

        $customerRequest = CustomerRequest::where('admin_id', auth()->id())->findOrFail($id);
        $customerRequest->status = $validated['status'];
        $customerRequest->save();

        return response()->json($customerRequest);
    }

    public function destroy($id)
    {
        $customerRequest = CustomerRequest::where('admin_id', auth()->id())->findOrFail($id);
        $customerRequest->delete();

        return response()->json(null, 204);
    }
}
