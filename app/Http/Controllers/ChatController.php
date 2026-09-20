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
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $customerId = Auth::guard('customer')->id();

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

        // Check if this is their very first message
        $isFirstMessage = !Chat::where('customer_id', $customerId)->exists();

        $chat = Chat::create([
            'customer_id' => $customerId,
            'message' => $message ?? '',
            'image_path' => $imagePath,
            'sender' => 'customer',
            'status' => 'sent'
        ]);

        // If it was their first message, send an automated welcome reply immediately
        if ($isFirstMessage) {
            Chat::create([
                'customer_id' => $customerId,
                'message' => 'Hello! Welcome to Escalona\'s Quail Farm. How can we help you today?',
                'image_path' => null,
                'sender' => 'admin',
                'status' => 'sent'
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

        // Check if admin has sent a recent message
        // within the last 10 seconds - for typing indicator
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

        return response()->json([
            'success' => true,
            'typing' => $isTyping
        ]);
    }

    public function submitRating(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $customerId = Auth::guard('customer')->id();

        session([
            'chat_rating' => $request->rating,
            'chat_rating_comments' => $request->comments
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}