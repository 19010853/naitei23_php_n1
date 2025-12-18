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
     * Tạo mới đánh giá (review) cho một tour.
     *
     * - Chỉ cho phép khi user đã đăng nhập.
     * - Mỗi user chỉ được review 1 lần cho mỗi tour.
     */
    public function store(Request $request, Tour $tour)
    {
        $user = $this->requireAuthenticatedUserForReview();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        // Check nếu user đã review tour này trước đó
        if ($this->hasExistingReviewForTour($user->id, $tour->id)) {
            return response()->json([
                'success' => false,
                'message' => __('common.you_already_reviewed_this_tour'),
            ], 422);
        }

        $validated = $this->validateReviewPayload($request);

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
     * Cập nhật review.
     *
     * - Chỉ cho phép khi user đăng nhập và là chủ sở hữu review.
     */
    public function update(Request $request, Review $review)
    {
        $user = $this->requireAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if ($response = $this->forbidIfNotOwner($review->user_id, $user->id)) {
            return $response;
        }

        $validated = $this->validateReviewPayload($request);

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
     * Xóa review hiện tại.
     *
     * - Cập nhật lại rating trung bình của tour sau khi xóa.
     */
    public function destroy(Review $review)
    {
        $user = $this->requireAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if ($response = $this->forbidIfNotOwner($review->user_id, $user->id)) {
            return $response;
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
     * Lấy review của user hiện tại cho một tour (nếu có).
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

    /**
     * Bắt buộc user phải đăng nhập để tạo review mới.
     * Trả về User nếu hợp lệ, hoặc JsonResponse nếu chưa đăng nhập.
     */
    protected function requireAuthenticatedUserForReview()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_review'),
            ], 401);
        }

        return $user;
    }

    /**
     * Bắt buộc user phải đăng nhập (dùng chung cho update/destroy/getUserReview).
     */
    protected function requireAuthenticatedUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login'),
            ], 401);
        }

        return $user;
    }

    /**
     * Trả về true nếu user đã có review cho tour chỉ định.
     */
    protected function hasExistingReviewForTour(int $userId, int $tourId): bool
    {
        return Review::where('user_id', $userId)
            ->where('tour_id', $tourId)
            ->exists();
    }

    /**
     * Validate payload review (rating + content).
     */
    protected function validateReviewPayload(Request $request): array
    {
        return $request->validate([
            'rating' => 'required|integer|in:1,2,3,4,5',
            'content' => 'nullable|string|max:2000',
        ]);
    }

    /**
     * Trả về 403 nếu user hiện tại không phải chủ sở hữu.
     */
    protected function forbidIfNotOwner(int $ownerId, int $currentUserId): ?\Illuminate\Http\JsonResponse
    {
        if ($ownerId !== $currentUserId) {
            return response()->json([
                'success' => false,
                'message' => __('common.unauthorized'),
            ], 403);
        }

        return null;
    }
}
