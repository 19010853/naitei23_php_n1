<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateTourScheduleRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Role;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminManagementController extends Controller
{
    private const TOUR_IMAGE_DIR = 'images/tours';
    private const CATEGORY_IMAGE_DIR = 'images/categories';

    /**
     * Display a listing of users with search and filter
     */
    public function users(Request $request)
    {
        $query = $request->input('query');
        $roleId = $request->input('role_id');

        $usersQuery = User::with('roles');

        // Search filter (name or email)
        if (!empty($query)) {
            // Escape LIKE wildcards to prevent LIKE injection
            $escapeLike = function ($value) {
                return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
            };
            $escapedQuery = $escapeLike($query);
            
            $usersQuery->where(function($q) use ($escapedQuery) {
                $q->whereRaw("name LIKE ? ESCAPE '\\'", ["%{$escapedQuery}%"])
                  ->orWhereRaw("email LIKE ? ESCAPE '\\'", ["%{$escapedQuery}%"]);
            });
        }

        // Role filter
        if (!empty($roleId) && $roleId !== 'all') {
            $usersQuery->whereHas('roles', function($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        $users = $usersQuery->latest()->paginate(12);
        $roles = Role::all();

        // Keep search parameters in pagination links
        $users->appends($request->only(['query', 'role_id']));

        // If AJAX request, return only the table content
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'table_html' => view('admin.pages.users-table', compact('users'))->render(),
                'pagination_html' => view('admin.pages.users-pagination', compact('users'))->render(),
            ]);
        }

        return view('admin.pages.users', compact('users', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function storeUser(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $roleIds = $validated['role_ids'];
        unset($validated['role_ids']);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->roles()->attach($roleIds);

        return redirect()->route('admin.users')->with('success', __('common.user_created_successfully'));
    }

    /**
     * Update the specified user
     */
    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $roleIds = $validated['role_ids'];
        unset($validated['role_ids']);

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users')->with('success', __('common.user_updated_successfully'));
    }

    /**
     * Remove the specified user
     */
    public function deleteUser(Request $request, User $user)
    {
        // Prevent deletion of current logged in user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => __('common.cannot_delete_own_account')
            ], 403);
        }

        $user->delete();

        if (! $request->ajax() && ! $request->expectsJson()) {
            session()->flash('success', __('common.user_deleted_successfully'));
        }

        return response()->json([
            'success' => true,
            'message' => __('common.user_deleted_successfully')
        ]);
    }

    /**
     * Manage Tour Categories
     */
    public function categories()
    {
        $categories = Category::withCount('tours')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.pages.categories', compact('categories'));
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::CATEGORY_IMAGE_DIR)) {
            $validated['image_url'] = $imagePath;
        }

        Category::create($validated);

        return redirect()->route('admin.categories')->with('success', __('common.category_saved_successfully'));
    }

    public function updateCategory(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::CATEGORY_IMAGE_DIR, $category->image_url)) {
            $validated['image_url'] = $imagePath;
        }

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', __('common.category_saved_successfully'));
    }

    public function deleteCategory(Request $request, Category $category)
    {
        if ($category->tours()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('common.cannot_delete_category_with_tours'),
            ], 400);
        }

        $this->deleteImage($category->image_url);
        $category->delete();

        if (! $request->ajax() && ! $request->expectsJson()) {
            session()->flash('success', __('common.category_deleted_successfully'));
        }

        return response()->json([
            'success' => true,
            'message' => __('common.category_deleted_successfully'),
        ]);
    }

    /**
     * Manage Tours (general information)
     */
    public function tours()
    {
        $tours = Tour::with(['category'])
            ->withCount('schedules')
            ->withAvg('reviews', 'rating')
            ->orderBy('name')
            ->paginate(12);

        $categories = Category::orderBy('name')->get();

        return view('admin.pages.tours', compact('tours', 'categories'));
    }

    public function storeTour(StoreTourRequest $request)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::TOUR_IMAGE_DIR)) {
            $validated['image_url'] = $imagePath;
        }

        Tour::create($validated);

        return redirect()->route('admin.tours')->with('success', __('common.tour_saved_successfully'));
    }

    public function updateTour(UpdateTourRequest $request, Tour $tour)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::TOUR_IMAGE_DIR, $tour->image_url)) {
            $validated['image_url'] = $imagePath;
        }

        $tour->update($validated);

        return redirect()->route('admin.tours')->with('success', __('common.tour_saved_successfully'));
    }

    public function deleteTour(Request $request, Tour $tour)
    {
        // Prevent deletion if tour has associated schedules
            if ($tour->schedules()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.cannot_delete_tour_with_schedules')
                ], 400);
            }

        $this->deleteImage($tour->image_url);
        $tour->delete();

        if (! $request->ajax() && ! $request->expectsJson()) {
            session()->flash('success', __('common.tour_deleted_successfully'));
        }

            return response()->json(['success' => true, 'message' => __('common.tour_deleted_successfully')]);
    }

    /**
     * Manage Tour Schedules (specific schedules)
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

            return redirect()->route('admin.tour-schedules')->with('success', __('common.schedule_saved_successfully'));
    }

    public function updateTourSchedule(UpdateTourScheduleRequest $request, TourSchedule $tourSchedule)
    {
        $validated = $request->validated();
            $tourSchedule->update($validated);

            return redirect()->route('admin.tour-schedules')->with('success', __('common.schedule_saved_successfully'));
    }

    public function deleteTourSchedule(Request $request, TourSchedule $tourSchedule)
    {
        // Prevent deletion if schedule has associated bookings
            if ($tourSchedule->bookings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.cannot_delete_schedule_with_bookings')
                ], 400);
            }

        $tourSchedule->delete();

        if (! $request->ajax() && ! $request->expectsJson()) {
            session()->flash('success', __('common.schedule_deleted_successfully'));
        }

            return response()->json(['success' => true, 'message' => __('common.schedule_deleted_successfully')]);
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'tourSchedule.tour'])
            ->latest()
            ->paginate(12);

        return view('admin.pages.bookings', compact('bookings'));
    }

    /**
     * Export all bookings to PDF for admin reporting.
     */
    public function exportBookingsPdf()
    {
        $bookings = Booking::with(['user', 'tourSchedule.tour'])
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('admin.pdf.bookings', [
            'bookings' => $bookings,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('admin-bookings-' . now()->format('Ymd_His') . '.pdf');
    }

    public function payments()
    {
        $payments = Payment::with(['booking.user', 'booking.tourSchedule.tour'])
            ->latest('payment_date')
            ->paginate(12);

        // Check if there's a payment notification from webhook
        if (session('payment_notification')) {
            session()->flash('success', session('payment_notification_message', __('common.new_payment_notification')));
            session()->forget('payment_notification');
            session()->forget('payment_notification_message');
        }

        return view('admin.pages.payments', compact('payments'));
    }

    public function reviews()
    {
        $reviews = Review::with(['user', 'tour'])
            ->latest()
            ->paginate(12);

        // Check if there's a new review notification from session
        if (session('new_review_notification')) {
            session()->flash('success', session('new_review_message', __('common.review_comment_created')));
            session()->forget('new_review_notification');
            session()->forget('new_review_message');
        }

        return view('admin.pages.reviews', compact('reviews'));
    }

    public function comments()
    {
        $comments = Comment::with(['user', 'commentable'])
            ->latest()
            ->paginate(12);

        // Check if there's a new comment notification from session
        if (session('new_comment_notification')) {
            session()->flash('success', session('new_comment_message', __('common.review_comment_created')));
            session()->forget('new_comment_notification');
            session()->forget('new_comment_message');
        }

        return view('admin.pages.comments', compact('comments'));
    }

    /**
     * Admin reply to a review (similar to App Store/Google Play Developer Reply)
     */
    public function replyToReview(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'admin_replied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('common.admin_reply_added_successfully'),
            'review' => $review->load('user', 'tour')
        ]);
    }

    /**
     * Update admin reply to a review
     */
    public function updateAdminReply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'admin_replied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('common.admin_reply_updated_successfully'),
            'review' => $review->load('user', 'tour')
        ]);
    }

    /**
     * Delete admin reply from a review
     */
    public function deleteAdminReply(Review $review)
    {
        $review->update([
            'admin_reply' => null,
            'admin_replied_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('common.admin_reply_deleted_successfully')
        ]);
    }

    /**
     * Upload an image file to S3.
     *
     * @param \Illuminate\Http\UploadedFile|null $image The uploaded image file
     * @param string $directory The target directory path (e.g., 'images/tours' or 'images/categories')
     * @param string|null $oldPath Optional path to old image to delete from S3
     * @return string|null The full path to the uploaded image in S3, or null if no image provided
     * @throws \Exception If file operations fail
     */
    private function uploadImage(?UploadedFile $image, string $directory, ?string $oldPath = null): ?string
    {
        if (!$image) {
            return null;
        }

        // Delete old image if exists
        $this->deleteImage($oldPath);

        try {
            // Generate unique filename
            $imageName = Str::uuid() . '.' . $image->guessExtension();
            
            // Full path in S3
            $s3Path = $directory . '/' . $imageName;
            
            // Upload to S3
            Storage::disk('s3')->put($s3Path, file_get_contents($image->getRealPath()), 'public');
            
            // Return the S3 path (will be used to store in database)
            return $s3Path;
        } catch (\Exception $e) {
            \Log::error('Failed to upload image to S3: ' . $e->getMessage());
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Delete an image file from S3.
     *
     * @param string|null $path The S3 path to the image file
     * @return void
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('s3')->exists($path)) {
            try {
                Storage::disk('s3')->delete($path);
            } catch (\Exception $e) {
                \Log::error('Failed to delete image from S3: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get the full URL for an image stored in S3.
     *
     * @param string|null $path The S3 path to the image
     * @return string|null The full URL to the image, or null if path is empty
     */
    public static function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // If path already contains http/https, return as is (for backward compatibility)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Get URL from S3
        return Storage::disk('s3')->url($path);
    }
}

