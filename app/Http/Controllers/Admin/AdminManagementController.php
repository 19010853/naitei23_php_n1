<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Requests\UpdateTourScheduleRequest;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    /**
     * Hằng số định nghĩa thư mục lưu ảnh tour và category trên S3/local.
     * Đặt ở controller để dễ tìm kiếm cấu hình liên quan tới quản trị.
     */
    private const TOUR_IMAGE_DIR = 'images/tours';
    private const CATEGORY_IMAGE_DIR = 'images/categories';

    /**
     * Display a listing of users with search and filter
     *
     * Danh sách user kèm search + filter role.
     * Logic query được tách xuống hàm riêng để controller dễ đọc hơn.
     */
    public function users(Request $request)
    {
        $users = $this->buildUsersQuery($request)->latest()->paginate(12);
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
     *
     * Tạo mới user: validate qua FormRequest, hash mật khẩu, gán roles.
     */
    public function storeUser(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $roleIds = $validated['role_ids'];
        unset($validated['role_ids']);

        // Hash password trước khi lưu
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->roles()->attach($roleIds);

        return redirect()->route('admin.users')->with('success', __('common.user_created_successfully'));
    }

    /**
     * Update the specified user
     *
     * Cập nhật thông tin user + roles, chỉ đổi mật khẩu nếu có nhập mới.
     */
    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $roleIds = $validated['role_ids'];
        unset($validated['role_ids']);

        // Chỉ cập nhật password nếu người dùng nhập
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
     *
     * Không cho phép user tự xóa chính mình, còn lại xóa bình thường.
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
     *
     * Lấy danh sách category (kèm số tour) cho trang quản trị.
     */
    public function categories()
    {
        $categories = Category::withCount('tours')
            ->orderBy('name')
            ->get();

        return view('admin.pages.categories', compact('categories'));
    }

    /**
     * Lưu category mới, có hỗ trợ upload ảnh.
     */
    public function storeCategory(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::CATEGORY_IMAGE_DIR)) {
            $validated['image_url'] = $imagePath;
        }

        Category::create($validated);

        return redirect()->route('admin.categories')->with('success', __('common.category_saved_successfully'));
    }

    /**
     * Cập nhật category (bao gồm xử lý ảnh nếu có).
     */
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

        // Xóa file ảnh (nếu có) và record category
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
     *
     * Danh sách tour, kèm category, số schedule và rating trung bình.
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

    /**
     * Lưu tour mới, có hỗ trợ upload ảnh.
     */
    public function storeTour(StoreTourRequest $request)
    {
        $validated = $request->validated();

        if ($imagePath = $this->uploadImage($request->file('image'), self::TOUR_IMAGE_DIR)) {
            $validated['image_url'] = $imagePath;
        }

        Tour::create($validated);

        return redirect()->route('admin.tours')->with('success', __('common.tour_saved_successfully'));
    }

    /**
     * Cập nhật tour hiện tại.
     */
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
        // Prevent deletion if tour has associated schedules (rule nghiệp vụ nằm ở controller)
        if ($tour->schedules()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => __('common.cannot_delete_tour_with_schedules'),
            ], 400);
        }

        // Xóa ảnh + bản ghi tour
        $this->deleteImage($tour->image_url);
        $tour->delete();

        if (! $request->ajax() && ! $request->expectsJson()) {
            session()->flash('success', __('common.tour_deleted_successfully'));
        }

        return response()->json(['success' => true, 'message' => __('common.tour_deleted_successfully')]);
    }

    /**
     * Manage Tour Schedules (specific schedules)
     *
     * Danh sách lịch tour, kèm số booking cho quản trị.
     */
    public function tourSchedules()
    {
        $tours = Tour::orderBy('name')->get();
        $schedules = TourSchedule::with('tour')
            ->withCount('bookings')
            ->latest('start_date')
            ->get();

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
                'message' => __('common.cannot_delete_schedule_with_bookings'),
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
        // Danh sách booking kèm user & tour schedule để tránh N+1 trên view
        $bookings = Booking::with(['user', 'tourSchedule.tour'])
            ->latest()
            ->get();

        return view('admin.pages.bookings', compact('bookings'));
    }

    /**
     * Export all bookings to PDF for admin reporting.
     */
    public function exportBookingsPdf()
    {
        // Lấy toàn bộ booking kèm quan hệ cần thiết
        $bookings = Booking::with(['user', 'tourSchedule.tour'])
            ->orderByDesc('created_at')
            ->get();

        // Sinh PDF với DomPDF (giữ nguyên nội dung view)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.bookings', [
            'bookings' => $bookings,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('admin-bookings-' . now()->format('Ymd_His') . '.pdf');
    }

    public function payments()
    {
        // Danh sách payment kèm booking, user, tour để tránh N+1
        $payments = Payment::with(['booking.user', 'booking.tourSchedule.tour'])
            ->latest('payment_date')
            ->get();

        // Check if there's a payment notification from webhook
        if (session('payment_notification')) {
            session()->flash('success', session('payment_notification_message', __('common.new_payment_notification')));
            session()->forget('payment_notification');
            session()->forget('payment_notification_message');
        }

        return view('admin.pages.payments', compact('payments'));
    }

    /**
     * Download a single payment invoice PDF from admin panel.
     *
     * Admin can view/download invoice of any payment.
     */
    public function downloadPaymentInvoice(Payment $payment)
    {
        // Force English for invoice layout (same as customer)
        app()->setLocale('en');

        // Eager load relations used in invoice view
        $payment->load(['booking.tourSchedule.tour', 'booking.user']);

        $data = [
            'payment' => $payment,
            'booking' => $payment->booking,
            'tour' => $payment->booking->tourSchedule->tour,
            'tourSchedule' => $payment->booking->tourSchedule,
            'customer' => $payment->booking->user,
            'company' => [
                'name' => config('app.name', 'Tour Booking System'),
                'address' => config('app.company_address', '123 Main Street, City, Country'),
                'phone' => config('app.company_phone', '+84 123 456 789'),
                'email' => config('app.company_email', 'info@example.com'),
                'tax_id' => config('app.company_tax_id', 'TAX-123456'),
            ],
        ];

        $pdf = Pdf::loadView('customer.pdf.invoice', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('enable-font-subsetting', true);
        $pdf->setOption('isUnicode', true);
        $pdf->setOption('dpi', 96);

        $fileName = 'Invoice-' . ($payment->invoice_id ?? ('PAYMENT-' . $payment->id)) . '.pdf';

        return $pdf->download($fileName);
    }

    public function reviews()
    {
        // Review kèm user & tour để hiển thị ở trang admin
        $reviews = Review::with(['user', 'tour'])
            ->latest()
            ->get();

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
        // Comment kèm user & commentable (review) cho admin
        $comments = Comment::with(['user', 'commentable'])
            ->latest()
            ->get();

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
     * Xây dựng query users với filter theo từ khóa và role.
     * Tách riêng để method users() gọn hơn, dễ unit test.
     */
    protected function buildUsersQuery(Request $request)
    {
        $query = $request->input('query');
        $roleId = $request->input('role_id');

        $usersQuery = User::with('roles');

        // Tìm kiếm theo tên hoặc email (có escape LIKE để an toàn)
        if (!empty($query)) {
            $escapeLike = static function (string $value): string {
                return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
            };

            $escapedQuery = $escapeLike($query);

            $usersQuery->where(function ($q) use ($escapedQuery) {
                $q->whereRaw("name LIKE ? ESCAPE '\\\\'", ["%{$escapedQuery}%"])
                    ->orWhereRaw("email LIKE ? ESCAPE '\\\\'", ["%{$escapedQuery}%"]);
            });
        }

        // Lọc theo role nếu có chọn
        if (!empty($roleId) && $roleId !== 'all') {
            $usersQuery->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        return $usersQuery;
    }

    /**
     * Upload ảnh lên S3 (và xóa ảnh cũ nếu có).
     *
     * Tách thành hàm riêng để tái sử dụng cho tour & category, giữ controller gọn hơn.
     *
     * @throws \Exception
     */
    private function uploadImage(?UploadedFile $image, string $directory, ?string $oldPath = null): ?string
    {
        if (!$image) {
            return null;
        }

        // Xóa ảnh cũ nếu có
        $this->deleteImage($oldPath);

        try {
            $imageName = Str::uuid() . '.' . $image->guessExtension();
            $s3Path = $directory . '/' . $imageName;

            Storage::disk('s3')->put($s3Path, file_get_contents($image->getRealPath()), 'public');

            return $s3Path;
        } catch (\Exception $e) {
            \Log::error('Failed to upload image to S3: ' . $e->getMessage());

            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Xóa ảnh trên S3 hoặc local (nếu tồn tại).
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

        // Fallback xóa trên local storage
        try {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                \Log::info('Image deleted from local storage: ' . $path);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete image from local storage: ' . $e->getMessage());
        }
    }
}

