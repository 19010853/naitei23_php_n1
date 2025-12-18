<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * Model Tour.
 *
 * Thông tin tour du lịch (tên, mô tả, địa điểm, ảnh, rating trung bình...),
 * liên kết với Category, TourSchedule, Review, Comment và Like.
 */
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

    /**
     * Tour thuộc về một category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Tour có nhiều lịch (TourSchedule).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id');
    }

    /**
     * Tour có nhiều review.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'tour_id');
    }

    /**
     * Tour có nhiều comment (polymorphic).
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Tour có nhiều like (polymorphic).
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Cập nhật lại điểm trung bình (average_rating) dựa trên tất cả review.
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
     * Lấy số sao hiển thị dựa trên average_rating.
     *
     * Quy tắc:
     * - .0 - .4 → làm tròn xuống
     * - .5 - .9 → làm tròn lên
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

    /**
     * Lấy URL đầy đủ cho ảnh tour.
     *
     * - Hỗ trợ cả S3 và local storage.
     * - Nếu đã là URL tuyệt đối (http/https) thì trả nguyên.
     */
    public function getImageUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        // If already a full URL (http/https), return as is
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // Check if file exists in S3
        if (Storage::disk('s3')->exists($value)) {
            return Storage::disk('s3')->url($value);
        }

        // Fallback to local asset (for backward compatibility)
        return asset($value);
    }
}
