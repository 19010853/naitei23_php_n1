<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * Danh sách các field KHÔNG bị trim (xoá khoảng trắng đầu/cuối).
     *
     * Mật khẩu và xác nhận mật khẩu được giữ nguyên để tránh làm sai dữ liệu nhập.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
