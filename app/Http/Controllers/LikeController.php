<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Toggle like trên một review (like nếu chưa like, bỏ like nếu đã like).
     *
     * - Yêu cầu user đăng nhập.
     */
    public function toggle(Request $request, Review $review)
    {
        $user = $this->requireAuthenticatedUserToLike();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        [$isLiked] = $this->toggleLikeForReview($user->id, $review);

        $likesCount = $review->likes()->count();

        return response()->json([
            'success' => true,
            'message' => $isLiked ? __('common.review_liked') : __('common.review_unliked'),
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Kiểm tra user hiện tại đã like review hay chưa.
     *
     * - Nếu chưa đăng nhập: luôn trả về is_liked = false.
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

    /**
     * Bắt buộc user đăng nhập khi thao tác like/unlike.
     */
    protected function requireAuthenticatedUserToLike()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_like'),
            ], 401);
        }

        return $user;
    }

    /**
     * Thực hiện toggle like trên review cho một user cụ thể.
     *
     * @return array{0: bool} Trả về [isLikedSauKhiToggle]
     */
    protected function toggleLikeForReview(int $userId, Review $review): array
    {
        $existingLike = Like::where('user_id', $userId)
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
                'user_id' => $userId,
                'likeable_id' => $review->id,
                'likeable_type' => Review::class,
            ]);
            $isLiked = true;
        }

        return [$isLiked];
    }
}
