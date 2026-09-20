<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function index()
    {
        // Get all customers who have sent at least one message themselves
        $customers = Customer::whereHas('chats', function ($query) {
                $query->where('sender', 'customer');
            })
            ->with(['chats' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get();

        // Get unread count
        $unreadCount = Chat::where('sender', 'customer')
            ->where('status', 'sent')
            ->count();

        return view('admin.chat', compact(
            'customers',
            'unreadCount'
        ));
    }

    public function getCustomerChat($customerId)
    {
        $chats = Chat::where('customer_id', $customerId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Chat::where('customer_id', $customerId)
            ->where('sender', 'customer')
            ->where('status', 'sent')
            ->update([
                'status' => 'read'
            ]);

        // Check if customer is typing
        $typing = Chat::where('customer_id', $customerId)
            ->where('sender', 'customer')
            ->where('status', 'sent')
            ->where('updated_at', '>=', now()->subSeconds(10))
            ->exists();

        $customer = Customer::findOrFail($customerId);

        return response()->json([
            'success' => true,
            'chats' => $chats,
            'customer' => $customer,
            'typing' => $typing
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $message = $request->input('message');
        $hasImage = $request->hasFile('image');

        // Message or image is required
        if ((empty($message) || trim($message) === '') && !$hasImage) {
            return response()->json([
                'success' => false,
                'message' => 'Message or image is required.'
            ], 422);
        }

        // Store image if attached
        $imagePath = null;

        if ($hasImage) {
            $file = $request->file('image');

            $imagePath = $file->store(
                'chat_attachments',
                'public'
            );
        }

        $chat = Chat::create([
            'customer_id' => $request->customer_id,
            'message' => $message ?? '',
            'image_path' => $imagePath,
            'sender' => 'admin',
            'status' => 'sent'
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    public function getUnreadCount()
    {
        $unreadCount = Chat::where('sender', 'customer')
            ->where('status', 'sent')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }
}