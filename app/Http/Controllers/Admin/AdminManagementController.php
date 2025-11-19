<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Http\Requests\UpdateTourScheduleRequest;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    private const TOUR_IMAGE_DIR = 'images/tours';
    public function users()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(12);

        return view('admin.pages.users', compact('users'));
    }

    /**
     * Quản lý Tours (thông tin chung)
     */
    public function tours()
    {
        $tours = Tour::withCount('schedules')
            ->withAvg('reviews', 'rating')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.pages.tours', compact('tours'));
    }

    public function storeTour(StoreTourRequest $request)
    {
        $validated = $request->validated();
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
            $extension = $image->guessExtension();

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors(['image' => 'Invalid image extension.'])->withInput();
            }

            // Tạo thư mục nếu chưa tồn tại
            $imageDir = public_path(self::TOUR_IMAGE_DIR);
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            $imageName = Str::uuid() . '.' . $extension;
            $image->move($imageDir, $imageName);
            $imagePath = self::TOUR_IMAGE_DIR . '/' . $imageName;
            $validated['image_url'] = $imagePath;
        }

        Tour::create($validated);

        return redirect()->route('admin.tours')->with('success', 'Tour đã được thêm thành công.');
    }

    public function updateTour(UpdateTourRequest $request, Tour $tour)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Xóa image cũ nếu có
            if ($tour->image_url && File::exists(public_path($tour->image_url))) {
                File::delete(public_path($tour->image_url));
            }

            $image = $request->file('image');
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
            $extension = $image->guessExtension();

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors(['image' => 'Invalid image extension.'])->withInput();
            }

            // Tạo thư mục nếu chưa tồn tại
            $imageDir = public_path(self::TOUR_IMAGE_DIR);
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            $imageName = Str::uuid() . '.' . $extension;
            $image->move($imageDir, $imageName);
            $validated['image_url'] = self::TOUR_IMAGE_DIR . '/' . $imageName;
        }

        $tour->update($validated);

        return redirect()->route('admin.tours')->with('success', 'Tour đã được cập nhật thành công.');
    }

    public function deleteTour(Tour $tour)
    {
        // Prevent deletion if tour has associated schedules
        if ($tour->schedules()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa tour có lịch trình liên quan.'
            ], 400);
        }

        // Xóa image file nếu có
        if ($tour->image_url && File::exists(public_path($tour->image_url))) {
            File::delete(public_path($tour->image_url));
        }

        $tour->delete();

        return response()->json(['success' => true, 'message' => 'Tour đã được xóa thành công.']);
    }

    /**
     * Quản lý Tour Schedules (lịch trình cụ thể)
     */
    public function tourSchedules()
    {
        $tours = Tour::orderBy('name')->get();
        $schedules = TourSchedule::with('tour')
            ->withCount('bookings')
            ->latest('start_date')
            ->paginate(12);

        return view('admin.pages.tour-schedules', compact('schedules', 'tours'));
    }

    public function storeTourSchedule(StoreTourScheduleRequest $request)
    {
        $validated = $request->validated();
        TourSchedule::create($validated);

        return redirect()->route('admin.tour-schedules')->with('success', 'Lịch trình tour đã được thêm thành công.');
    }

    public function updateTourSchedule(UpdateTourScheduleRequest $request, TourSchedule $tourSchedule)
    {
        $validated = $request->validated();
        $tourSchedule->update($validated);

        return redirect()->route('admin.tour-schedules')->with('success', 'Lịch trình tour đã được cập nhật thành công.');
    }

    public function deleteTourSchedule(TourSchedule $tourSchedule)
    {
        // Prevent deletion if schedule has associated bookings
        if ($tourSchedule->bookings()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa lịch trình có booking liên quan.'
            ], 400);
        }

        $tourSchedule->delete();

        return response()->json(['success' => true, 'message' => 'Lịch trình tour đã được xóa thành công.']);
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

