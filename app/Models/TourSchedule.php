<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model TourSchedule.
 *
 * Thể hiện một lịch khởi hành cụ thể của tour (ngày bắt đầu/kết thúc, giá, sức chứa).
 */
class TourSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'price',
        'max_participants',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
    ];

    /**
     * Lịch tour thuộc về một tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    /**
     * Lịch tour có nhiều booking.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tour_schedule_id');
    }

    /**
     * Tổng số người đã được đặt cho lịch này.
     *
     * - Chỉ tính booking có status 'pending' hoặc 'confirmed'.
     * - Ưu tiên dùng kết quả withSum nếu đã eager load, tránh N+1.
     */
    public function getBookedParticipantsAttribute(): int
    {
        // Use withSum result if available (from eager loading)
        if (isset($this->attributes['bookings_sum_num_participants'])) {
            return (int) ($this->attributes['bookings_sum_num_participants'] ?? 0);
        }

        // Otherwise, query the database
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('num_participants');
    }

    /**
     * Số chỗ còn trống cho lịch này (thuộc tính ảo available_slots).
     */
    public function getAvailableSlotsAttribute(): int
    {
        $booked = $this->booked_participants;
        $available = $this->max_participants - $booked;
        return max(0, $available); // Ensure it's never negative
    }

    /**
     * Kiểm tra lịch đã full chỗ hay chưa.
     */
    public function isFullyBooked(): bool
    {
        return $this->booked_participants >= $this->max_participants;
    }
}
