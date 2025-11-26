<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
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
        Route::get('payments', [AdminManagementController::class, 'payments'])->name('payments');
        Route::get('reviews', [AdminManagementController::class, 'reviews'])->name('reviews');
        Route::get('comments', [AdminManagementController::class, 'comments'])->name('comments');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Customer routes (public with rate limiting)
Route::get('/categories', [\App\Http\Controllers\CustomerController::class, 'categories'])
    ->middleware('throttle:60,1')
    ->name('customer.categories');
Route::get('/tours/{tour}', [\App\Http\Controllers\CustomerController::class, 'tours'])
    ->middleware('throttle:60,1')
    ->name('customer.tours');
Route::get('/tours/{tour}/details', [\App\Http\Controllers\CustomerController::class, 'tourDetails'])
    ->middleware('throttle:60,1')
    ->name('customer.tour.details');

Route::get('locale/{locale}', [LanguageController::class, 'changeLanguage'])->name('locale.switch');

// Social authentication routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';