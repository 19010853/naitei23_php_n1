<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Booking.
 *
 * Đại diện cho một lần đặt tour của khách hàng, liên kết với User, TourSchedule và các Payment.
 */
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

    /**
     * Booking thuộc về một user (khách hàng tạo booking).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Booking thuộc về một lịch tour cụ thể.
     */
    public function tourSchedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class, 'tour_schedule_id');
    }

    /**
     * Booking có thể có nhiều lần thanh toán (Payment).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
}

