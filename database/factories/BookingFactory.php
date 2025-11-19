<?php

namespace Database\Factories;

use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
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
