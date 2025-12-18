<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 *
 * Factory sinh dữ liệu giả cho Tour (tên tour, slug, mô tả, địa điểm, ảnh).
 */
class TourFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của Tour.
     *
     * - Sử dụng danh sách tên tour cố định để dữ liệu seed dễ hiểu trên UI.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tourNames = [
            'Hà Nội - Hạ Long 3N2Đ',
            'Sài Gòn - Đà Lạt 4N3Đ',
            'Bangkok - Pattaya 5N4Đ',
            'Phú Quốc Discovery 4N3Đ',
            'Nha Trang - Đà Lạt 5N4Đ',
            'Hội An - Huế 4N3Đ',
            'Sapa - Fansipan 3N2Đ',
            'Mũi Né - Phan Thiết 3N2Đ',
            'Singapore - Malaysia 6N5Đ',
            'Bali - Indonesia 5N4Đ',
        ];

        $name = fake()->randomElement($tourNames);
        $slug = Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999);

        $locations = [
            'Quảng Ninh, Việt Nam',
            'Lâm Đồng, Việt Nam',
            'Bangkok & Pattaya, Thái Lan',
            'Phú Quốc, Việt Nam',
            'Khánh Hòa, Việt Nam',
            'Quảng Nam & Thừa Thiên Huế, Việt Nam',
            'Lào Cai, Việt Nam',
            'Bình Thuận, Việt Nam',
            'Singapore & Malaysia',
            'Bali, Indonesia',
        ];

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->paragraph(5),
            'location' => fake()->randomElement($locations),
            'image_url' => 'https://images.unsplash.com/photo-' . fake()->numberBetween(1500000000000, 1600000000000) . '?w=800&h=600&fit=crop',
        ];
    }
}
