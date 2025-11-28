<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Toggle like on a review (like if not liked, unlike if already liked).
     */
    public function toggle(Request $request, Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_like')
            ], 401);
        }

        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_id', $review->id)
            ->where('likeable_type', Review::class)
            ->first();

        if ($existingLike) {
            // Unlike (remove like)
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Like (create like)
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $review->id,
                'likeable_type' => Review::class,
            ]);
            $isLiked = true;
        }

        $likesCount = $review->likes()->count();

        return response()->json([
            'success' => true,
            'message' => $isLiked ? __('common.review_liked') : __('common.review_unliked'),
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Check if user has liked a review.
     */
    public function check(Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'is_liked' => false,
                'likes_count' => $review->likes()->count()
            ]);
        }

        $isLiked = $review->isLikedBy($user);
        $likesCount = $review->likes()->count();

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
