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
use Illuminate\Support\Str;

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
    public function deleteUser(User $user)
    {
        // Prevent deletion of current logged in user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => __('common.cannot_delete_own_account')
            ], 403);
        }

        $user->delete();

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

        return redirect()->route('admin.categories')->with('success', __('common.category_created_successfully'));
    }

    public function updateCategory(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::CATEGORY_IMAGE_DIR, $category->image_url)) {
            $validated['image_url'] = $imagePath;
        }

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', __('common.category_updated_successfully'));
    }

    public function deleteCategory(Category $category)
    {
        if ($category->tours()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('common.cannot_delete_category_with_tours'),
            ], 400);
        }

        $this->deleteImage($category->image_url);
        $category->delete();

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

        return redirect()->route('admin.tours')->with('success', __('common.tour_added_successfully'));
    }

    public function updateTour(UpdateTourRequest $request, Tour $tour)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::TOUR_IMAGE_DIR, $tour->image_url)) {
            $validated['image_url'] = $imagePath;
        }

        $tour->update($validated);

        return redirect()->route('admin.tours')->with('success', __('common.tour_updated_successfully'));
    }

    public function deleteTour(Tour $tour)
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

            return redirect()->route('admin.tour-schedules')->with('success', __('common.schedule_added_successfully'));
    }

    public function updateTourSchedule(UpdateTourScheduleRequest $request, TourSchedule $tourSchedule)
    {
        $validated = $request->validated();
            $tourSchedule->update($validated);

            return redirect()->route('admin.tour-schedules')->with('success', __('common.schedule_updated_successfully'));
    }

    public function deleteTourSchedule(TourSchedule $tourSchedule)
    {
        // Prevent deletion if schedule has associated bookings
            if ($tourSchedule->bookings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.cannot_delete_schedule_with_bookings')
                ], 400);
            }

        $tourSchedule->delete();

            return response()->json(['success' => true, 'message' => __('common.schedule_deleted_successfully')]);
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

    /**
     * Upload an image file to the specified directory.
     *
     * @param \Illuminate\Http\UploadedFile|null $image The uploaded image file
     * @param string $directory The target directory path (relative to public)
     * @param string|null $oldPath Optional path to old image to delete
     * @return string|null The relative path to the uploaded image, or null if no image provided
     * @throws \Exception If file operations fail
     */
    private function uploadImage(?UploadedFile $image, string $directory, ?string $oldPath = null): ?string
    {
        if (!$image) {
            return null;
        }

        $this->deleteImage($oldPath);

        try {
            $imageDir = public_path($directory);
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            $imageName = Str::uuid() . '.' . $image->guessExtension();
            $image->move($imageDir, $imageName);

            return $directory . '/' . $imageName;
        } catch (\Exception $e) {
            \Log::error('Failed to upload image: ' . $e->getMessage());
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Delete an image file from the public directory.
     *
     * @param string|null $path The relative path to the image file
     * @return void
     */
    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            try {
                File::delete(public_path($path));
            } catch (\Exception $e) {
                \Log::error('Failed to delete image: ' . $e->getMessage());
            }
        }
    }
}

