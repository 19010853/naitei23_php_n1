<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Middleware kiểm tra quyền truy cập admin cho các route quản trị.
     *
     * - Nếu chưa đăng nhập: chuyển hướng về trang đăng nhập admin.
     * - Nếu đã đăng nhập nhưng không có role 'Admin': trả về lỗi 403.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if (! $user || ! $user->hasRole('Admin')) {
            abort(403, 'Bạn không có quyền truy cập nội dung này.');
        }

        return $next($request);
    }
}


