<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model Like.
 *
 * Thể hiện hành động "thích" (like) của user trên các thực thể khác (review, ...).
 */
class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Like thuộc về một user (người nhấn like).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Đối tượng được like (review, ...).
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}

