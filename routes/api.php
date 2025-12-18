<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Định nghĩa các route API cho ứng dụng.
| Các route này được load qua RouteServiceProvider với prefix "api" và
| middleware group "api".
|
*/

// Trả về thông tin user hiện tại, yêu cầu đã đăng nhập qua Sanctum.
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'currentUser']);
