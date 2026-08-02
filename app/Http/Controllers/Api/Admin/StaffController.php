<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staffs = Staff::where('admin_id', $request->user()->id)->get();
        return response()->json($staffs);
    }

    public function show(Request $request, $id)
    {
        $staff = Staff::where('admin_id', $request->user()->id)->findOrFail($id);

        return response()->json($staff);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:staffs,email',
            'role' => 'required|string|max:255',
            'workingdays' => 'required|array',
            'workingdays.*.day' => 'required|string',
            'workingdays.*.start' => 'required|string',
            'workingdays.*.end' => 'required|string',
        ]);

        $staff = Staff::create([
            'admin_id' => $request->user()->id,
            'fname' => $validated['firstName'],
            'lname' => $validated['lastName'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'schedule' => $validated['workingdays'],
        ]);

        return response()->json($staff, 201);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::where('admin_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:staffs,email,'.$staff->id,
            'role' => 'sometimes|string|max:255',
            'workingdays' => 'sometimes|array',
            'workingdays.*.day' => 'sometimes|string',
            'workingdays.*.start' => 'sometimes|string',
            'workingdays.*.end' => 'sometimes|string',
        ]);

        $map = [
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'email',
            'role' => 'role',
            'workingdays' => 'schedule',
        ];

        foreach ($map as $inputKey => $dbKey) {
            if (array_key_exists($inputKey, $validated)) {
                $staff->$dbKey = $validated[$inputKey];
            }
        }

        $staff->save();

        return response()->json($staff);
    }

    public function destroy(Request $request, $id)
    {
        $staff = Staff::where('admin_id', $request->user()->id)->findOrFail($id);
        $staff->delete();

        return response()->json(null, 204);
    }

    public function changeRole(Request $request, $id)
    {
        $staff = Staff::where('admin_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|max:255',
        ]);

        $staff->role = $validated['role'];
        $staff->save();

        return response()->json($staff);
    }

    public function leaderboard(Request $request)
    {
        $adminId = $request->user()->id;

        $staffs = Staff::where('admin_id', $adminId)
            ->get()
            ->map(function ($staff) {
                $orderCount = $staff->orders()->count();

                return [
                    'id' => $staff->id,
                    'fname' => $staff->fname,
                    'lname' => $staff->lname,
                    'role' => $staff->role,
                    'orders_count' => $orderCount,
                    'schedule' => $staff->schedule,
                ];
            })
            ->sortByDesc('orders_count')
            ->values();

        return response()->json($staffs);
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|exists:staffs,id',
            'order_id' => 'required|string|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $order->staff_id = $validated['staff_id'];
        $order->save();

        return response()->json($order);
    }

    public function assignments(Request $request)
    {
        $adminId = $request->user()->id;

        $query = Order::where('admin_id', $adminId)
            ->whereNotNull('staff_id')
            ->with('staff');

        if ($request->query('table_id')) {
            $query->where('table_id', $request->query('table_id'));
        }
        if ($request->query('staff_id')) {
            $query->where('staff_id', $request->query('staff_id'));
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'table_id' => $order->table_id,
                'status' => $order->status,
                'order' => $order->order,
                'staff' => $order->staff ? [
                    'id' => $order->staff->id,
                    'fname' => $order->staff->fname,
                    'lname' => $order->staff->lname,
                    'role' => $order->staff->role,
                ] : null,
                'created_at' => $order->created_at,
            ];
        });

        return response()->json($orders);
    }
}