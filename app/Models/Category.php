<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
     * Category owns many tours.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'category_id');
    }

    /**
     * Get the full URL for the category image.
     * Automatically handles both S3 and local storage.
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


