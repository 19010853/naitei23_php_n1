<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tour_schedule_id',
        'booking_date',
        'num_participants',
        'total_price',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_price' => 'decimal:2',
        'num_participants' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tourSchedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class, 'tour_schedule_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
}

