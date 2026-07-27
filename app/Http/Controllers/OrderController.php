<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Public customer-facing endpoints (admin_id scoped)
    public function index(Request $request, string $admin_id)
    {
        $orders = Order::where('admin_id', '=', $admin_id)->latest()->get();

        return response()->json($orders);
    }

    public function store(Request $request, string $admin_id)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'nullable|string',
            'table_id' => 'required|string',
            'order' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::create([
            'admin_id' => $admin_id,
            'table_id' => $request->table_id,
            'order' => $request->order,
        ]);

        return response()->json($order, 201);
    }

    public function show(Request $request, string $admin_id, string $id)
    {
        $order = Order::where('admin_id', '=', $admin_id)->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function update(Request $request, string $admin_id, string $id)
    {
        $order = Order::where('admin_id', '=', $admin_id)->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'order' => 'sometimes|array',
            'status' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update($request->only(['order', 'status']));

        return response()->json($order);
    }

    public function destroy(Request $request, string $admin_id, string $id)
    {
        $order = Order::where('admin_id', '=', $admin_id)->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted'], 200);
    }

    // Admin-scoped endpoints
    public function adminIndex(Request $request)
    {
        $query = Order::where('admin_id', auth()->id())->with('staff');

        if ($request->query('status')) {
            $statusMap = [
                'pending' => 0,
                'confirmed' => 1,
                'cooking' => 2,
                'served' => 3,
                'completed' => 4,
                'cancelled' => 5,
            ];
            $status = $statusMap[$request->query('status')] ?? null;
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        if ($request->query('table_id')) {
            $query->where('table_id', $request->query('table_id'));
        }

        if ($request->query('staff_id')) {
            $query->where('staff_id', $request->query('staff_id'));
        }

        if ($request->query('date')) {
            $query->whereDate('created_at', $request->query('date'));
        }

        $orders = $query->latest()->paginate(20);

        return response()->json($orders);
    }

    public function adminShow($id)
    {
        $order = Order::where('admin_id', auth()->id())->with(['staff', 'admin'])->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1,2,3,4,5',
        ]);

        $order = Order::where('admin_id', auth()->id())->findOrFail($id);
        $order->status = (int) $validated['status'];
        $order->save();

        return response()->json($order);
    }

    public function updateOrderTime(Request $request, $id)
    {
        $validated = $request->validate([
            'order_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $order = Order::where('admin_id', auth()->id())->findOrFail($id);
        $order->order_time = $validated['order_time'];
        $order->save();

        return response()->json($order);
    }

    public function todayCount()
    {
        $count = Order::where('admin_id', auth()->id())
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return response()->json(['count' => $count]);
    }
}
