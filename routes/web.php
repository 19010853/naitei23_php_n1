<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
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

Route::get('/', function () {
    return view('welcome');
});

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

// Customer routes
Route::get('/categories', [\App\Http\Controllers\CustomerController::class, 'categories'])->name('customer.categories');
Route::get('/tours/{tour}', [\App\Http\Controllers\CustomerController::class, 'tours'])->name('customer.tours');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';