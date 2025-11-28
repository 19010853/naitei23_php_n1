<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment on a review.
     */
    public function store(Request $request, Review $review)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login_to_comment')
            ], 401);
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

        return response()->json([
            'success' => true,
            'message' => __('common.comment_created_successfully'),
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login')
            ], 401);
        }

        // Check if comment belongs to user
        if ($comment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('common.unauthorized')
            ], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => __('common.comment_updated_successfully'),
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('common.please_login')
            ], 401);
        }

        // Check if comment belongs to user
        if ($comment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('common.unauthorized')
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => __('common.comment_deleted_successfully')
        ]);
    }
}
