<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    /**
     * Seed demo tours and schedules for admin UI.
     *
     * @return void
     */
    public function run()
    {
        $categoryIds = Category::pluck('id', 'slug')->toArray();

        if (empty($categoryIds)) {
            $this->command->warn('No categories found. Run CategorySeeder before TourSeeder.');
            return;
        }

        $tours = [
            [
                'category_slug' => 'du-lich-trong-nuoc',
                'name' => 'Hà Nội - Hạ Long 3N2Đ',
                'description' => 'Khám phá kỳ quan thiên nhiên thế giới Vịnh Hạ Long với du thuyền sang trọng.',
                'location' => 'Quảng Ninh, Việt Nam',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
                'schedules' => [
                    ['start_date' => now()->addDays(7), 'end_date' => now()->addDays(9), 'price' => 3490000, 'max_participants' => 25],
                    ['start_date' => now()->addDays(21), 'end_date' => now()->addDays(23), 'price' => 3650000, 'max_participants' => 30],
                ],
            ],
            [
                'category_slug' => 'du-lich-nuoc-ngoai',
                'name' => 'Bangkok - Pattaya 5N4Đ',
                'description' => 'Thưởng thức ẩm thực đường phố, tham quan cung điện và đảo San hô tuyệt đẹp.',
                'location' => 'Bangkok & Pattaya, Thái Lan',
                'image_url' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34',
                'schedules' => [
                    ['start_date' => now()->addDays(10), 'end_date' => now()->addDays(15), 'price' => 11990000, 'max_participants' => 20],
                ],
            ],
            [
                'category_slug' => 'du-lich-bien-dao',
                'name' => 'Phú Quốc Discovery 4N3Đ',
                'description' => 'Trải nghiệm biển đảo Phú Quốc với resort 5*, VinWonders và Safari.',
                'location' => 'Phú Quốc, Việt Nam',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
                'schedules' => [
                    ['start_date' => now()->addDays(5), 'end_date' => now()->addDays(8), 'price' => 7890000, 'max_participants' => 18],
                    ['start_date' => now()->addDays(30), 'end_date' => now()->addDays(33), 'price' => 8150000, 'max_participants' => 22],
                ],
            ],
        ];

        $defaultCategoryId = reset($categoryIds);

        foreach ($tours as $tourData) {
            $categoryId = Arr::get($categoryIds, $tourData['category_slug'], $defaultCategoryId);

            $slug = Str::slug($tourData['slug'] ?? $tourData['name']);

            $tour = Tour::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'name' => $tourData['name'],
                    'slug' => $slug,
                    'description' => $tourData['description'],
                    'location' => $tourData['location'],
                    'image_url' => $tourData['image_url'],
                ]
            );

            foreach ($tourData['schedules'] as $schedule) {
                TourSchedule::updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'start_date' => $schedule['start_date'],
                    ],
                    [
                        'end_date' => $schedule['end_date'],
                        'price' => $schedule['price'],
                        'max_participants' => $schedule['max_participants'],
                    ]
                );
            }
        }
    }
}


