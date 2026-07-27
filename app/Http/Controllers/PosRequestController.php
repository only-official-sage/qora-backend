<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PosRequest;
use Illuminate\Http\Request;

class PosRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PosRequest::where('admin_id', auth()->id())->with('order');

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        $requests = $query->latest()->get();

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $order = Order::where('admin_id', auth()->id())->findOrFail($validated['order_id']);

        $posRequest = PosRequest::create([
            'admin_id' => auth()->id(),
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json($posRequest, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $posRequest = PosRequest::where('admin_id', auth()->id())->findOrFail($id);
        $posRequest->status = $validated['status'];
        $posRequest->save();

        return response()->json($posRequest);
    }

    public function destroy($id)
    {
        $posRequest = PosRequest::where('admin_id', auth()->id())->findOrFail($id);
        $posRequest->delete();

        return response()->json(null, 204);
    }
}
