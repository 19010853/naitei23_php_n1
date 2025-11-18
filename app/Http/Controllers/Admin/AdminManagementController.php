<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;

class AdminManagementController extends Controller
{
    public function users()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(12);

        return view('admin.pages.users', compact('users'));
    }

    public function categories()
    {
        $categories = Category::withCount('tours')
            ->orderBy('name')
            ->get();

        return view('admin.pages.categories', compact('categories'));
    }

    public function tours()
    {
        $tours = Tour::with('category')
            ->withCount(['schedules', 'reviews', 'likes'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('admin.pages.tours', compact('tours'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'tourSchedule.tour'])
            ->latest()
            ->paginate(12);

        return view('admin.pages.bookings', compact('bookings'));
    }

    public function payments()
    {
        $payments = Payment::with(['booking.user'])
            ->latest('payment_date')
            ->paginate(12);

        return view('admin.pages.payments', compact('payments'));
    }

    public function reviews()
    {
        $reviews = Review::with(['user', 'tour'])
            ->latest()
            ->paginate(12);

        return view('admin.pages.reviews', compact('reviews'));
    }

    public function comments()
    {
        $comments = Comment::with(['user', 'commentable'])
            ->latest()
            ->paginate(12);

        return view('admin.pages.comments', compact('comments'));
    }
}

