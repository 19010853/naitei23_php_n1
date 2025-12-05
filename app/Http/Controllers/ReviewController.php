<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(Request $request, Tour $tour)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_review')
            ], 401);
        }

        // Check if user already has a review for this tour
        $existingReview = Review::where('user_id', $user->id)
            ->where('tour_id', $tour->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => __('common.you_already_reviewed_this_tour')
            ], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|in:1,2,3,4,5',
            'content' => 'nullable|string|max:2000',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'tour_id' => $tour->id,
            'rating' => $validated['rating'],
            'content' => $validated['content'] ?? null,
        ]);

        // Update tour's average rating
        $tour->updateAverageRating();

        $review->load('user', 'likes', 'comments.user');

        // Flash message for admin notification (will be shown when admin visits reviews page)
        session()->flash('new_review_notification', true);
        session()->flash('new_review_message', __('common.review_comment_created'));

        // Flash message for customer flow when redirected
        if (! $request->expectsJson()) {
            $request->session()->flash('success', __('common.review_created_successfully'));

            return redirect()->back();
        }

        return response()->json([
            'success' => true,
            'message' => __('common.review_created_successfully'),
            'review' => $review
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login')
            ], 401);
        }

        // Check if review belongs to user
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('common.unauthorized')
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|in:1,2,3,4,5',
            'content' => 'nullable|string|max:2000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'content' => $validated['content'] ?? null,
        ]);

        // Update tour's average rating
        $review->tour->updateAverageRating();

        $review->load('user', 'likes', 'comments.user');

        if (! $request->expectsJson()) {
            $request->session()->flash('success', __('common.review_updated_successfully'));

            return redirect()->back();
        }

        return response()->json([
            'success' => true,
            'message' => __('common.review_updated_successfully'),
            'review' => $review
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login')
            ], 401);
        }

        // Check if review belongs to user
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('common.unauthorized')
            ], 403);
        }

        // Get tour before deleting review
        $tour = $review->tour;
        
        $review->delete();

        // Update tour's average rating after deletion
        $tour->updateAverageRating();

        return response()->json([
            'success' => true,
            'message' => __('common.review_deleted_successfully')
        ]);
    }

    /**
     * Get user's review for a tour (if exists).
     */
    public function getUserReview(Tour $tour)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'review' => null
            ]);
        }

        $review = Review::where('user_id', $user->id)
            ->where('tour_id', $tour->id)
            ->with('user', 'likes', 'comments.user')
            ->first();

        return response()->json([
            'success' => true,
            'review' => $review
        ]);
    }
}
