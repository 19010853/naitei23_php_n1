<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class EngagementSeeder extends Seeder
{
    /**
     * Seed reviews, comments and likes for tour detail/demo pages.
     *
     * @return void
     */
    public function run()
    {
        $users = User::take(3)->get();
        $tours = Tour::with('category')->take(3)->get();

        if ($users->isEmpty() || $tours->isEmpty()) {
            $this->command->warn('Need users and tours before running EngagementSeeder.');
            return;
        }

        foreach ($tours as $index => $tour) {
            foreach ($users as $userIndex => $user) {
                $rating = max(3, 5 - $userIndex + $index % 2);

                $review = Review::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'tour_id' => $tour->id,
                    ],
                    [
                        'rating' => min($rating, 5),
                        'content' => sprintf(
                            'Tour %s mang đến trải nghiệm tuyệt vời cho gia đình tôi. %s',
                            $tour->name,
                            $userIndex === 0 ? 'Hướng dẫn viên thân thiện và chuyên nghiệp.' : 'Lịch trình hợp lý, đồ ăn ngon.'
                        ),
                    ]
                );

                Comment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'commentable_id' => $tour->id,
                        'commentable_type' => Tour::class,
                    ],
                    [
                        'body' => sprintf(
                            'Bạn nên chuẩn bị thêm %s để chuyến đi trọn vẹn hơn.',
                            $userIndex === 0 ? 'đồ bơi và kem chống nắng' : 'máy ảnh để ghi lại khoảnh khắc'
                        ),
                    ]
                );

                Like::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'likeable_id' => $tour->id,
                        'likeable_type' => Tour::class,
                    ],
                    []
                );
            }
        }
    }
}


