<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 *
 * Factory sinh dữ liệu giả cho Review (đánh giá tour).
 */
class ReviewFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của Review.
     *
     * - rating từ 3 đến 5 để ưu tiên đánh giá tích cực trong demo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviews = [
            'Tour rất tuyệt vời, hướng dẫn viên nhiệt tình và chuyên nghiệp. Chắc chắn sẽ quay lại!',
            'Trải nghiệm tuyệt vời, lịch trình hợp lý, đồ ăn ngon. Đáng giá từng đồng.',
            'Tour đẹp nhưng hơi vội, cần thêm thời gian tham quan một số địa điểm.',
            'Dịch vụ tốt, xe đưa đón đúng giờ, khách sạn sạch sẽ và tiện nghi.',
            'Hướng dẫn viên rất am hiểu về lịch sử và văn hóa địa phương. Rất hài lòng!',
            'Tour phù hợp cho gia đình, trẻ em cũng rất thích. Sẽ giới thiệu cho bạn bè.',
            'Giá cả hợp lý so với chất lượng dịch vụ. Điểm đến đẹp và ấn tượng.',
            'Có một số điểm cần cải thiện nhưng nhìn chung tour khá tốt.',
        ];

        return [
            'user_id' => User::factory(),
            'tour_id' => Tour::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'content' => fake()->randomElement($reviews),
        ];
    }
}
