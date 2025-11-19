<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tourId = Tour::inRandomOrder()->first()?->id ?? Tour::factory();
        $tour = is_int($tourId) ? Tour::find($tourId) : Tour::factory()->create();

        return [
            'user_id' => User::factory(),
            'likeable_id' => $tour->id,
            'likeable_type' => Tour::class,
        ];
    }
}
