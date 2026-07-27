<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

class ShopOrderController extends Controller
{
    public function index()
    {
        $orders = ShopOrder::where('admin_id', auth()->id())->latest()->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = ShopOrder::where('admin_id', auth()->id())->findOrFail($id);

        return response()->json($order);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|string|exists:shop_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
        ]);

        $adminId = auth()->id();
        $total = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $product = ShopProduct::where('admin_id', $adminId)->findOrFail($item['product_id']);

            if ($product->stock < $item['quantity']) {
                return response()->json(['message' => "Insufficient stock for {$product->name}"], 422);
            }

            $lineTotal = $product->price * $item['quantity'];
            $total += $lineTotal;

            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $item['quantity'],
                'line_total' => $lineTotal,
            ];

            $product->stock -= $item['quantity'];
            $product->save();
        }

        $order = ShopOrder::create([
            'admin_id' => $adminId,
            'user_id' => $request->user_id ?? null,
            'items' => $items,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        return response()->json($order, 201);
    }
}
