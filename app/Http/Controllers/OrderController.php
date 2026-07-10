<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request, string $admin_id)
    {
        $orders = Order::where('admin_id', '=', $admin_id, 'and')->latest()->get();
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
        $order = Order::where('admin_id', '=', $admin_id, 'and')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function update(Request $request, string $admin_id, string $id)
    {
        $order = Order::where('admin_id', '=', $admin_id, 'and')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'staff_id' => 'sometimes|nullable|string',
            'table_id' => 'sometimes|string',
            'status' => 'sometimes|integer',
            'report_status' => 'sometimes|integer:in:0,1,2,3',
            'report_reason' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update($request->only(['staff_id', 'table_id', 'order', 'status', 'report_status', 'report_reason']));

        return response()->json($order);
    }

    public function destroy(Request $request, string $admin_id, string $id)
    {
        $order = Order::where('admin_id', '=', $admin_id, 'and')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted'], 200);
    }
}
