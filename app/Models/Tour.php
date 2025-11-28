<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'location',
        'image_url',
        'average_rating',
    ];

    protected $casts = [
        'average_rating' => 'decimal:1',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id');
    }

    /**
     * Tour has many reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'tour_id');
    }

    /**
     * Tour has many comments (polymorphic)
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Tour has many likes (polymorphic)
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Update average rating based on all reviews
     */
    public function updateAverageRating(): void
    {
        $avg = $this->reviews()->avg('rating');
        
        if ($avg === null) {
            $this->average_rating = null;
        } else {
            // Round to 1 decimal place: round($avg * 10) / 10
            $this->average_rating = round($avg * 10) / 10;
        }
        
        $this->save();
    }

    /**
     * Get stars display value based on average_rating
     * Rules:
     * .0 - .4 → round down
     * .5 - .9 → round up
     */
    public function getStarsDisplayAttribute(): int
    {
        // If average_rating is null, try to calculate from reviews
        $rating = $this->average_rating;
        if ($rating === null) {
            $avg = $this->reviews()->avg('rating');
            if ($avg === null) {
                return 0;
            }
            $rating = round($avg * 10) / 10;
        }

        $decimal = $rating - floor($rating);
        $stars = $decimal <= 0.4 
            ? floor($rating)
            : ceil($rating);

        // Ensure stars_display is between 1 and 5, but allow 0 if no reviews
        if ($stars <= 0) {
            return 0;
        }
        return min(5, max(1, (int)$stars));
    }
}
