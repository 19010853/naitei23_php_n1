<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 *
 * Factory sinh dữ liệu giả cho Like (lượt thích cho tour).
 */
class LikeFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của Like.
     *
     * - Gán likeable là một Tour bất kỳ.
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
