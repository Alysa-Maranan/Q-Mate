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
                : ($customer->barangay . ', ' . $customer->municipality . ', ' . $customer->province),
            'product' => $request->product,
            'quantity' => (int)$request->quantity,
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
        $order = Order::where('customer_id', auth('customer')->id())->findOrFail($id);

        return view('order-receipt', compact('order'));
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:10',
        ]);

        $order = Order::where('customer_id', auth('customer')->id())->findOrFail($id);

        // Only allow cancellation of pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled.'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }

    // Admin: Update order status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,to_ship,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'status' => $order->status
        ]);
    }
}