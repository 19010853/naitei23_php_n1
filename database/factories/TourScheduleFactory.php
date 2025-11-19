<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourSchedule>
 */
class TourScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+6 months');
        $days = fake()->numberBetween(2, 7);
        $endDate = (clone $startDate)->modify("+{$days} days");

        $basePrice = fake()->numberBetween(2000000, 15000000);
        $price = round($basePrice / 1000) * 1000; // Round to nearest 1000

        return [
            'tour_id' => Tour::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price,
            'max_participants' => fake()->numberBetween(15, 40),
        ];
    }
}
