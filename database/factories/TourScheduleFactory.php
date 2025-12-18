<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourSchedule>
 *
 * Factory sinh dữ liệu giả cho TourSchedule (lịch khởi hành, giá, sức chứa).
 */
class TourScheduleFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của TourSchedule.
     *
     * - Ngày bắt đầu từ hiện tại đến 6 tháng sau.
     * - Giá được làm tròn tới 1,000 VND để dữ liệu nhìn đẹp hơn.
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
