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

    /**
     * Cancel customer order
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $customer = auth('customer')->user();

        // Make sure the order belongs to the currently logged-in customer
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Only pending orders can be cancelled
        if ($order->status !== 'pending') {
            return back()->with(
                'error',
                'This order can no longer be cancelled.'
            );
        }

        // Save cancellation details
        $order->status = 'cancelled';
        $order->cancellation_reason = $request->reason;
        $order->cancelled_at = now();
        $order->save();

        return back()->with(
            'success',
            'Order cancelled successfully.'
        );
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