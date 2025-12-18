<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model Comment.
 *
 * Đại diện cho một bình luận của user trên các thực thể khác (review, ...), dùng morph.
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Comment thuộc về một user (người viết comment).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Đối tượng mà comment đang gắn vào (review, ...).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}

