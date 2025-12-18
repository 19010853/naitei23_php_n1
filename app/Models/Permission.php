<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model Permission.
 *
 * Thể hiện một quyền cụ thể trong hệ thống (ví dụ: manage-users), gán cho nhiều role.
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Permission có thể thuộc về nhiều role (many-to-many).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}

