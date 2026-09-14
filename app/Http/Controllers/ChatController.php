<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $chats = Chat::where('customer_id', $customerId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return view('customer.chat', compact('chats'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $customerId = Auth::guard('customer')->id();

        // Check if this is their very first message
        $isFirstMessage = !Chat::where('customer_id', $customerId)->exists();

        $chat = Chat::create([
            'customer_id' => $customerId,
            'message' => $request->message,
            'sender' => 'customer',
            'status' => 'sent'
        ]);

        // If it was their first message, send an automated welcome reply immediately
        if ($isFirstMessage) {
            Chat::create([
                'customer_id' => $customerId,
                'message' => 'Hello! Welcome to Escalona\'s Quail Farm. How can we help you today?',
                'sender' => 'admin',
                'status' => 'sent' // Mark as 'sent' so customer sees it as unread notification
            ]);
        }

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    public function getMessages()
    {
        $customerId = Auth::guard('customer')->id();
        $chats = Chat::where('customer_id', $customerId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        // Mark admin messages as read
        Chat::where('customer_id', $customerId)
            ->where('sender', 'admin')
            ->where('status', 'sent')
            ->update(['status' => 'read']);

        // Check if admin has sent a recent message (within last 10 seconds) - for typing indicator
        $typing = Chat::where('customer_id', $customerId)
            ->where('sender', 'admin')
            ->where('status', 'sent')
            ->where('updated_at', '>=', now()->subSeconds(10))
            ->exists();

        return response()->json([
            'success' => true,
            'chats' => $chats,
            'typing' => $typing
        ]);
    }

    public function setTyping(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        $isTyping = $request->input('typing', false);

        // Store typing status in session or cache if needed
        // For now, just acknowledge
        return response()->json(['success' => true, 'typing' => $isTyping]);
    }

    public function submitRating(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $customerId = Auth::guard('customer')->id();

        // Store rating in session or database as needed
        // For now, just acknowledge
        session(['chat_rating' => $request->rating, 'chat_rating_comments' => $request->comments]);

        return response()->json(['success' => true]);
    }
}