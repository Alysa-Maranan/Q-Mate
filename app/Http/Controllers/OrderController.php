<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $customer = auth('customer')->user();

        // Make sure the order belongs to the currently logged-in customer
        $order = Order::where('id', $id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Only pending orders can be cancelled
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This order can no longer be cancelled.'
            ], 422);
        }

        // Save cancellation details
        $order->status = 'cancelled';
        $order->cancellation_reason = $request->cancellation_reason;
        $order->cancelled_at = now();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
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

    /**
     * Permanently delete the selected orders only.
     *
     * Customer accounts, products, sales, payments, and any other data are
     * left untouched. Order line items are removed only when they actually
     * belong to the orders table (in this schema order_items is tied to
     * contact_messages, so it is intentionally preserved).
     */
    public function bulkDestroy(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|distinct',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));

        $deleted = 0;

        DB::transaction(function () use ($ids, &$deleted) {
            $existingIds = Order::whereIn('id', $ids)->pluck('id')->all();

            if (empty($existingIds)) {
                return;
            }

            // Only touch order line items if their foreign key really points
            // to the orders table.
            if (Schema::hasTable('order_items')) {
                $referencesOrders = collect(Schema::getForeignKeys('order_items'))
                    ->contains(fn ($foreignKey) => ($foreignKey['foreign_table'] ?? null) === 'orders');

                if ($referencesOrders) {
                    DB::table('order_items')->whereIn('order_id', $existingIds)->delete();
                }
            }

            $deleted = Order::whereIn('id', $existingIds)->delete();

            // Remove dashboard acknowledgement keys for the deleted orders.
            if (Schema::hasTable('admin_notification_acks')) {
                $ackKeys = [];
                foreach ($existingIds as $existingId) {
                    $ackKeys[] = 'order_'.$existingId;
                    $ackKeys[] = 'cancel_'.$existingId;
                }

                DB::table('admin_notification_acks')->whereIn('ack_key', $ackKeys)->delete();
            }
        });

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No matching orders were found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => $deleted === 1
                ? 'Order deleted permanently.'
                : $deleted.' orders deleted permanently.',
        ]);
    }
}