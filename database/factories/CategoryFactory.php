<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 *
 * Factory sinh dữ liệu giả cho Category (tên, slug, mô tả, ảnh minh hoạ).
 */
class CategoryFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của Category.
     *
     * - Lấy ngẫu nhiên một tên category từ danh sách cố định để dữ liệu dễ đọc hơn.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryNames = [
            'Du lịch trong nước',
            'Du lịch nước ngoài',
            'Du lịch biển đảo',
            'Du lịch văn hóa',
            'Du lịch mạo hiểm',
            'Du lịch nghỉ dưỡng',
            'Du lịch gia đình',
            'Du lịch cặp đôi',
        ];

        $name = fake()->unique()->randomElement($categoryNames);
        $slug = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->sentence(10),
            'image_url' => 'https://images.unsplash.com/photo-' . fake()->numberBetween(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop',
        ];
    }
}
