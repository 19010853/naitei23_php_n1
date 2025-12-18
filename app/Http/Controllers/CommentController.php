<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Tạo comment mới cho một review.
     *
     * - Yêu cầu user đăng nhập.
     */
    public function store(Request $request, Review $review)
    {
        $user = $this->requireAuthenticatedUserToComment();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'user_id' => $user->id,
            'body' => $validated['body'],
            'commentable_id' => $review->id,
            'commentable_type' => Review::class,
        ]);

        $comment->load('user');

        // Flash message for admin notification (will be shown when admin visits comments page)
        session()->flash('new_comment_notification', true);
        session()->flash('new_comment_message', __('common.review_comment_created'));

        if (! $request->expectsJson()) {
            $request->session()->flash('success', __('common.comment_created_successfully'));

            return redirect()->back();
        }

        return response()->json([
            'success' => true,
            'message' => __('common.comment_created_successfully'),
            'comment' => $comment
        ]);
    }

    /**
     * Cập nhật nội dung comment.
     *
     * - Chỉ cho phép khi user đăng nhập và là chủ sở hữu comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $user = $this->requireAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if ($response = $this->forbidIfNotOwner($comment->user_id, $user->id)) {
            return $response;
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);

        $comment->load('user');

        if (! $request->expectsJson()) {
            $request->session()->flash('success', __('common.comment_updated_successfully'));

            return redirect()->back();
        }

        return response()->json([
            'success' => true,
            'message' => __('common.comment_updated_successfully'),
            'comment' => $comment
        ]);
    }

    /**
     * Xóa một comment.
     *
     * - Require user đăng nhập và là chủ sở hữu.
     */
    public function destroy(Comment $comment)
    {
        $user = $this->requireAuthenticatedUser();
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if ($response = $this->forbidIfNotOwner($comment->user_id, $user->id)) {
            return $response;
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => __('common.comment_deleted_successfully')
        ]);
    }

    /**
     * Bắt buộc user đăng nhập để comment.
     * Dùng riêng message cho luồng comment.
     */
    protected function requireAuthenticatedUserToComment()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_comment'),
            ], 401);
        }

        return $user;
    }

    /**
     * Bắt buộc user đăng nhập (dùng cho update/destroy).
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
     * Trả về 403 nếu user hiện tại không phải chủ sở hữu comment.
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
