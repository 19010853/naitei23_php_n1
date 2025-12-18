<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Model Category.
 *
 * Nhóm các tour theo loại (ví dụ: biển, núi, tham quan...), có thể gắn ảnh đại diện.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
    ];

    /**
     * Một category sở hữu nhiều tour.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'category_id');
    }

    /**
     * Lấy URL đầy đủ cho ảnh category.
     *
     * - Hỗ trợ cả ảnh lưu trên S3 và local.
     * - Nếu giá trị đã là URL tuyệt đối (http/https) thì trả nguyên.
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


