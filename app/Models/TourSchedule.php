<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * TourSchedule belongs to Tour
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    /**
     * TourSchedule has many bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tour_schedule_id');
    }

    /**
     * Get the total number of booked participants for this schedule
     * Only count confirmed bookings (status = 'confirmed' or 'pending')
     * Uses withSum result if available, otherwise queries the database
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
     * Get the number of available slots
     */
    public function getAvailableSlotsAttribute(): int
    {
        $booked = $this->booked_participants;
        $available = $this->max_participants - $booked;
        return max(0, $available); // Ensure it's never negative
    }

    /**
     * Check if the schedule is fully booked
     */
    public function isFullyBooked(): bool
    {
        return $this->booked_participants >= $this->max_participants;
    }
}
