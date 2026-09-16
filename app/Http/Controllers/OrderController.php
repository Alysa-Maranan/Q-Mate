<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product' => 'required|string',
            'quantity' => 'required|string',
            'preferred_date' => 'nullable|date',
            'order_type' => 'required|in:pickup,delivery',
            'address' => 'required_if:order_type,delivery',
        ]);

        $customer = auth('customer')->user();

        // GCash proof upload removed — proof sent via chatbox instead
        $gcashProofPath = null;

        // Create order
        $order = Order::create([
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,

            'address' => $request->order_type === 'delivery'
                ? $request->address
                : 'Pagkakaisa, Naujan',

            'product' => $request->product,
            'quantity' => (int) $request->quantity,
            'notes' => $request->notes,
            'status' => 'pending',
            'order_type' => $request->order_type,
        ]);

        return back()->with([
            'order_success' => true,
            'order_id' => $order->id,
            'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
        ]);
    }

    public function receipt($id)
    {
        $order = Order::findOrFail($id);

        return view('order-receipt', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,to_ship,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'status' => $order->status,
        ]);
    }
}