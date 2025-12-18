<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 *
 * Factory sinh dữ liệu giả cho Comment (bình luận cho tour).
 */
class CommentFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của Comment.
     *
     * - Gán commentable là một Tour bất kỳ để tạo dữ liệu demo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Bạn nên chuẩn bị thêm đồ bơi và kem chống nắng để chuyến đi trọn vẹn hơn.',
            'Máy ảnh là điều không thể thiếu để ghi lại những khoảnh khắc đẹp.',
            'Nên mang theo thuốc say tàu xe nếu bạn dễ bị say.',
            'Thời tiết ở đây khá nóng, nên mang quần áo mát mẻ và nón.',
            'Đừng quên mang theo giấy tờ tùy thân và bảo hiểm du lịch.',
            'Nên đổi tiền trước khi đi để có tỷ giá tốt hơn.',
            'Mang theo ổ cắm đa năng vì ổ cắm ở đây có thể khác.',
            'Nên chuẩn bị một số từ vựng cơ bản của ngôn ngữ địa phương.',
        ];

        $tourId = Tour::inRandomOrder()->first()?->id ?? Tour::factory();
        $tour = is_int($tourId) ? Tour::find($tourId) : Tour::factory()->create();

        return [
            'user_id' => User::factory(),
            'body' => fake()->randomElement($comments),
            'commentable_id' => $tour->id,
            'commentable_type' => Tour::class,
        ];
    }
}
