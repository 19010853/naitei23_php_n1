<?php

namespace Database\Factories;

use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 *
 * Factory sinh dữ liệu giả cho Booking (dùng trong test/seed demo).
 */
class BookingFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của model Booking.
     *
     * - Random trạng thái booking (pending/confirmed/cancelled/completed).
     * - user_id và tour_schedule_id được tạo thông qua các factory liên quan.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $status = fake()->randomElement($statuses);
        $numParticipants = fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'tour_schedule_id' => TourSchedule::factory(),
            'booking_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'num_participants' => $numParticipants,
            'total_price' => 0, // Will be calculated by model observer or manually
            'status' => $status,
        ];
    }
}
