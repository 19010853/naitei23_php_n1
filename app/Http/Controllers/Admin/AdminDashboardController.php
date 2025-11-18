<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $statCards = [
            [
                'key' => 'total_users',
                'label' => 'Người dùng',
                'value' => User::count(),
                'trend' => [
                    'direction' => 'positive',
                    'text' => '+12% so với tháng trước',
                ],
            ],
            [
                'key' => 'total_categories',
                'label' => 'Danh mục',
                'value' => Category::count(),
                'trend' => null,
            ],
            [
                'key' => 'total_tours',
                'label' => 'Tour đang bán',
                'value' => Tour::count(),
                'trend' => [
                    'direction' => 'positive',
                    'text' => '+4 tour mới',
                ],
            ],
            [
                'key' => 'total_bookings',
                'label' => 'Booking',
                'value' => Booking::count(),
                'trend' => [
                    'direction' => 'positive',
                    'text' => '+8% so với tuần trước',
                ],
            ],
            [
                'key' => 'pending_bookings',
                'label' => 'Đang chờ xử lý',
                'value' => Booking::where('status', 'pending')->count(),
                'trend' => null,
            ],
            [
                'key' => 'successful_payments',
                'label' => 'Thanh toán thành công',
                'value' => Payment::where('status', 'success')->count(),
                'trend' => [
                    'direction' => 'positive',
                    'text' => '+3 giao dịch',
                ],
            ],
        ];

        $recentBookings = Booking::with([
            'user',
            'tourSchedule.tour',
        ])->latest()->take(5)->get();

        $topTours = Tour::with(['category'])
            ->withCount(['reviews', 'likes'])
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $recentReviews = Review::with(['user', 'tour'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.pages.dashboard', compact('statCards', 'recentBookings', 'topTours', 'recentReviews'));
    }

    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'total_tours' => Tour::count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'successful_payments' => Payment::where('status', 'success')->count(),
            'total_reviews' => Review::count(),
            'total_comments' => Comment::count(),
            'total_likes' => Like::count(),
        ]);
    }
}


