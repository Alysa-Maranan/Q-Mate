<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class CustomerController extends Controller
{
    public function showRegistrationForm()
    {
        return view('customer.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'barangay' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'barangay' => $request->barangay,
            'municipality' => $request->municipality,
            'province' => $request->province,
        ]);

        // Auto-login after registration
        Auth::guard('customer')->login($customer);
        
        return redirect()->route('customer.dashboard')->with('success', 'Account created successfully!');
    }

    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // If there's a redirect parameter, use that, otherwise go to dashboard
            $redirectTo = $request->query('redirect_to', route('customer.dashboard'));
            return redirect($redirectTo);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    public function dashboard()
    {
        $customer = auth('customer')->user();
        
        // Get order statistics
        $totalOrders = \App\Models\Order::where('customer_id', $customer->id)->count();
        $confirmedOrders = \App\Models\Order::where('customer_id', $customer->id)->where('status', 'confirmed')->count();
        $readyOrders = \App\Models\Order::where('customer_id', $customer->id)->where('status', 'to_ship')->count();
        $completedOrders = \App\Models\Order::where('customer_id', $customer->id)->where('status', 'completed')->count();
        $cancelledOrders = \App\Models\Order::where('customer_id', $customer->id)->where('status', 'cancelled')->count();
        $totalSpent = \App\Models\Order::where('customer_id', $customer->id)->where('status', 'completed')->sum('quantity') * 100; // Estimate
        
        // Get recent orders
        $recentOrders = \App\Models\Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get chart data for orders over time
        $orders = \App\Models\Order::where('customer_id', $customer->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->limit(30)
            ->get();
        
        $chartLabels = $orders->pluck('date')->toArray();
        $chartData = $orders->pluck('count')->toArray();

        return view('customer.dashboard', compact('totalOrders', 'confirmedOrders', 'readyOrders', 'completedOrders', 'cancelledOrders', 'totalSpent', 'recentOrders', 'chartLabels', 'chartData'));
    }

    public function profile()
    {
        $customer = auth('customer')->user();
        $recentOrders = \App\Models\Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('customer.profile', compact('recentOrders'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,' . auth('customer')->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = auth('customer')->user();
        $customer->update($request->all());

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    public function orders()
    {
        return redirect()->route('customer.orders.all');
    }

    public function allOrders()
    {
        $orders = \App\Models\Order::where('customer_id', auth('customer')->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.all', compact('orders'));
    }

    public function toPay()
    {
        $orders = \App\Models\Order::where('customer_id', auth('customer')->id())
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.topay', compact('orders'));
    }

    public function toShip()
    {
        $orders = \App\Models\Order::where('customer_id', auth('customer')->id())
            ->where('status', 'to_ship')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.toship', compact('orders'));
    }

    public function completed()
    {
        $orders = \App\Models\Order::where('customer_id', auth('customer')->id())
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.completed', compact('orders'));
    }

    public function cancelled()
    {
        $orders = \App\Models\Order::where('customer_id', auth('customer')->id())
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.cancelled', compact('orders'));
    }

    // Get customer notifications
    public function getNotifications()
    {
        $customer = auth('customer')->user();
        
        // Get recent order status changes
        $orders = \App\Models\Order::where('customer_id', $customer->id)
            ->whereIn('status', ['confirmed', 'to_ship', 'completed', 'cancelled'])
            ->where('updated_at', '>', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($order) {
                $messages = [
                    'confirmed' => 'Your order has been confirmed!',
                    'to_ship' => 'Your order is ready for pickup!',
                    'completed' => 'Your order has been completed. Thank you!',
                    'cancelled' => 'Your order has been cancelled.'
                ];

                return [
                    'id' => 'order_'.$order->id,
                    'message' => $messages[$order->status] ?? 'Order status updated',
                    'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'product' => ucwords(str_replace('_', ' ', $order->product)),
                    'quantity' => $order->quantity,
                    'total' => $order->formatted_total,
                    'status' => $order->status,
                    'status_label' => str_replace('_', ' ', ucfirst($order->status)),
                    'time' => $order->updated_at->diffForHumans(),
                    'timestamp' => $order->updated_at->timestamp,
                    'type' => 'order'
                ];
            });

        // Get recent unread chats from admin
        $chats = \App\Models\Chat::where('customer_id', $customer->id)
            ->where('sender', 'admin')
            ->where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($chat) {
                return [
                    'id' => 'chat_'.$chat->id,
                    'message' => 'New message from Admin',
                    'order_number' => 'Chat',
                    'status' => 'pending',
                    'time' => $chat->created_at->diffForHumans(),
                    'timestamp' => $chat->created_at->timestamp,
                    'type' => 'chat'
                ];
            });

        // Get reviews with admin replies
        $reviews = \App\Models\Review::where('customer_id', $customer->id)
            ->whereNotNull('admin_reply')
            ->where('updated_at', '>', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($review) {
                $productName = ucwords(str_replace(['-', '_'], ' ', $review->product_slug));
                return [
                    'id' => 'review_' . $review->id,
                    'message' => 'Admin replied to your review',
                    'order_number' => $productName,
                    'product' => $productName,
                    'product_slug' => $review->product_slug,
                    'status' => 'replied',
                    'time' => $review->updated_at->diffForHumans(),
                    'timestamp' => $review->updated_at->timestamp,
                    'type' => 'review_reply'
                ];
            });

        $notifications = $orders->concat($chats)->concat($reviews)->sortByDesc('timestamp')->values();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->count()
        ]);
    }

    public function markNotificationsRead()
    {
        $customer = auth('customer')->user();
        
        // Mark unread chats from admin as read
        \App\Models\Chat::where('customer_id', $customer->id)
            ->where('sender', 'admin')
            ->where('status', 'sent')
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function getOrderStatus($id)
    {
        $order = \App\Models\Order::where('customer_id', auth('customer')->id())->findOrFail($id);
        
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'updated_at' => $order->updated_at->toIso8601String()
        ]);
    }
}