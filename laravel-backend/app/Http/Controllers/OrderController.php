<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with('items');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('created_at')->get();
        return response()->json(['orders' => $orders]);
    }

    public function show(string $id)
    {
        $order = Order::with('items.product')->find($id);
        if (! $order) {
            return response()->json(['error' => 'Commande non trouvée'], 404);
        }
        return response()->json(['order' => $order]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'shipping_address' => 'required|array',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric',
            'items.*.supplier_price' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($data) {
            $orderId = Str::uuid()->toString();

            $supplierCost = 0;
            $margin = 0;
            foreach ($data['items'] as $item) {
                $supplierCost += $item['supplier_price'] * $item['quantity'];
                $margin += ($item['unit_price'] - $item['supplier_price']) * $item['quantity'];
            }

            $order = Order::create([
                'id' => $orderId,
                'customer_id' => $data['customer_id'] ?? null,
                'total_amount' => $data['total_amount'],
                'supplier_cost' => $supplierCost,
                'margin' => $margin,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => $data['shipping_address'],
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'id' => Str::uuid()->toString(),
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'supplier_price' => $item['supplier_price'],
                ]);
            }

            $order->load('items');
            return response()->json(['order' => $order], 201);
        });
    }

    public function update(Request $request, string $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['error' => 'Commande non trouvée'], 404);
        }

        $data = $request->validate([
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'tracking_number' => 'nullable|string',
        ]);

        $order->update($data);

        return response()->json(['order' => $order]);
    }
}

