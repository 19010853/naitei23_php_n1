<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Định nghĩa các route web (HTML) cho ứng dụng.
| Bao gồm:
| - Khu vực admin (prefix 'admin', middleware 'auth' + 'admin')
| - Route khách hàng (tours, booking, review/comment/like)
| - Social login (Google, Facebook)
| - Các route profile, invoice, payment history...
|
*/

Route::get('/', [\App\Http\Controllers\CustomerController::class, 'home'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AdminAuthController::class, 'destroy'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index']);
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

        Route::get('users', [AdminManagementController::class, 'users'])->name('users');
        Route::post('users', [AdminManagementController::class, 'storeUser'])->name('users.store');
        Route::put('users/{user}', [AdminManagementController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [AdminManagementController::class, 'deleteUser'])->name('users.delete');

        // Tour Categories
        Route::get('categories', [AdminManagementController::class, 'categories'])->name('categories');
        Route::post('categories', [AdminManagementController::class, 'storeCategory'])->name('categories.store');
        Route::put('categories/{category}', [AdminManagementController::class, 'updateCategory'])->name('categories.update');
        Route::delete('categories/{category}', [AdminManagementController::class, 'deleteCategory'])->name('categories.delete');
        
        // Tours CRUD (thông tin chung)
        Route::get('tours', [AdminManagementController::class, 'tours'])->name('tours');
        Route::post('tours', [AdminManagementController::class, 'storeTour'])->name('tours.store');
        Route::put('tours/{tour}', [AdminManagementController::class, 'updateTour'])->name('tours.update');
        Route::delete('tours/{tour}', [AdminManagementController::class, 'deleteTour'])->name('tours.delete');

        // Tour Schedules CRUD (lịch trình cụ thể)
        Route::get('tour-schedules', [AdminManagementController::class, 'tourSchedules'])->name('tour-schedules');
        Route::post('tour-schedules', [AdminManagementController::class, 'storeTourSchedule'])->name('tour-schedules.store');
        Route::put('tour-schedules/{tourSchedule}', [AdminManagementController::class, 'updateTourSchedule'])->name('tour-schedules.update');
        Route::delete('tour-schedules/{tourSchedule}', [AdminManagementController::class, 'deleteTourSchedule'])->name('tour-schedules.delete');
        
        Route::get('bookings', [AdminManagementController::class, 'bookings'])->name('bookings');
        Route::get('bookings/export/pdf', [AdminManagementController::class, 'exportBookingsPdf'])->name('bookings.export.pdf');
        Route::get('payments', [AdminManagementController::class, 'payments'])->name('payments');
        Route::get('payments/{payment}/invoice', [AdminManagementController::class, 'downloadPaymentInvoice'])->name('payments.invoice');
        Route::get('reviews', [AdminManagementController::class, 'reviews'])->name('reviews');
        Route::get('comments', [AdminManagementController::class, 'comments'])->name('comments');
        
        // Admin reply to reviews
        Route::post('reviews/{review}/reply', [AdminManagementController::class, 'replyToReview'])->name('reviews.reply');
        Route::put('reviews/{review}/reply', [AdminManagementController::class, 'updateAdminReply'])->name('reviews.reply.update');
        Route::delete('reviews/{review}/reply', [AdminManagementController::class, 'deleteAdminReply'])->name('reviews.reply.delete');
    });
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Image proxy route (to serve S3 images when bucket is not public)
Route::get('/image/{path}', [\App\Http\Controllers\ImageProxyController::class, 'proxy'])
    ->where('path', '.*')
    ->name('image.proxy');

// Customer routes (public with rate limiting)
Route::get('/categories', [\App\Http\Controllers\CustomerController::class, 'categories'])
    ->middleware('throttle:60,1')
    ->name('customer.categories');

// Handle booking redirect from tour page with schedule parameter
Route::get('/tours/{tour}', function (\App\Models\Tour $tour, \Illuminate\Http\Request $request) {
    $scheduleId = $request->query('schedule');
    if ($scheduleId) {
        $schedule = \App\Models\TourSchedule::findOrFail($scheduleId);
        if (auth()->check()) {
            return redirect()->route('booking.show', $schedule);
        } else {
            return redirect()->route('login', ['redirectTo' => route('booking.show', $schedule)]);
        }
    }
    return app(\App\Http\Controllers\CustomerController::class)->tours($tour);
})->middleware('throttle:60,1')->name('customer.tours');
Route::get('/tours/{tour}/details', [\App\Http\Controllers\CustomerController::class, 'tourDetails'])
    ->middleware('throttle:60,1')
    ->name('customer.tour.details');
Route::get('/tours/{tour}/details/pdf', [\App\Http\Controllers\CustomerController::class, 'exportTourDetailsPdf'])
    ->middleware('throttle:60,1')
    ->name('customer.tour.details.pdf');

// Booking routes (authenticated)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/booking/{schedule}', [\App\Http\Controllers\BookingController::class, 'show'])->name('booking.show');
    Route::post('/booking/{schedule}', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/success', [\App\Http\Controllers\BookingController::class, 'success'])->name('booking.success');
    Route::get('/booking/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('booking.cancel');
});

// Stripe webhook (optional - not required if using synchronous payment processing)
// Route::post('/webhook/stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
//     ->middleware('web')
//     ->name('webhook.stripe');

Route::get('locale/{locale}', [LanguageController::class, 'changeLanguage'])->name('locale.switch');

// Social authentication routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Payment History & Invoice routes
    Route::get('/payment-history', [\App\Http\Controllers\PaymentHistoryController::class, 'index'])->name('payment.history');
    Route::get('/invoice/{payment}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    
    // Review routes
    Route::post('/tours/{tour}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/tours/{tour}/reviews/user', [ReviewController::class, 'getUserReview'])->name('reviews.user');
    
    // Comment routes (on reviews)
    Route::post('/reviews/{review}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Like routes (on reviews)
    Route::post('/reviews/{review}/like', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::get('/reviews/{review}/like', [LikeController::class, 'check'])->name('likes.check');
});

require __DIR__.'/auth.php';