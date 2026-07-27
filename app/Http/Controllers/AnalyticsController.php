<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function tableAnalytics(Request $request, $tableId)
    {
        $adminId = $request->query('admin_id');

        $query = Order::where('table_id', $tableId);

        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        $orders = $query->with('staff')->latest()->get();

        $totalSpent = $orders->reduce(function ($carry, $order) {
            return $carry + collect($order->order)->sum(fn ($item) => $item['price'] * $item['quantity'] ?? 0);
        }, 0);

        return response()->json([
            'table_id' => $tableId,
            'total_orders' => $orders->count(),
            'total_spent' => $totalSpent,
            'orders' => $orders,
        ]);
    }

    public function roomAnalytics(Request $request)
    {
        $adminId = $request->query('admin_id');

        if (! $adminId) {
            return response()->json(['message' => 'admin_id required'], 422);
        }

        $orders = Order::where('admin_id', $adminId)
            ->where('table_id', 'like', 'room_%')
            ->orWhere('table_id', 'like', 'RM_%')
            ->latest()
            ->get();

        return response()->json([
            'admin_id' => $adminId,
            'total_room_orders' => $orders->count(),
            'orders' => $orders,
        ]);
    }

    public function orderHistory(Request $request)
    {
        $adminId = $request->query('admin_id');

        if (! $adminId) {
            return response()->json(['message' => 'admin_id is required'], 422);
        }

        $filter = $request->query('filter', 'all');

        $query = Order::where('admin_id', $adminId)->with('staff');

        if ($filter === 'date_range' && $request->query('start_date') && $request->query('end_date')) {
            $query->whereBetween('created_at', [
                $request->query('start_date'),
                $request->query('end_date'),
            ]);
        }

        $orders = $query->latest()->paginate(20);

        return response()->json($orders);
    }
}
