<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Customer: Submit a review
    public function store(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'customer_id' => Auth::guard('customer')->id(),
            'product_slug' => $request->product_slug,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('customer'),
        ]);
    }

    // Customer: Get reviews for a product (public)
    public function productReviews($slug)
    {
        $reviews = Review::with('customer')
            ->where('product_slug', $slug)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'reviews' => $reviews]);
    }

    // Admin: Get all reviews
    public function index()
    {
        $reviews = Review::with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reviews', compact('reviews'));
    }

    // Admin: Get all reviews (API for tab)
    public function indexApi()
    {
        $reviews = Review::with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'reviews' => $reviews]);
    }

    // Admin: Reply to a review
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:500',
        ]);

        $review = Review::findOrFail($id);
        $review->update([
            'admin_reply' => $request->admin_reply,
            'admin_reply_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('customer'),
        ]);
    }
}
