<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}


