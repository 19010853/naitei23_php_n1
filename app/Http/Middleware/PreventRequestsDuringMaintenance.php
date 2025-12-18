<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Các URI vẫn được truy cập khi ứng dụng bật chế độ bảo trì (maintenance).
     *
     * Có thể thêm các webhook health-check hoặc route đặc biệt vào đây nếu cần.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
