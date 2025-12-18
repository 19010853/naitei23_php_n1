<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Danh sách các URI được loại trừ khỏi kiểm tra CSRF.
     *
     * Ví dụ: webhook từ bên thứ ba (Stripe, PayPal, v.v...) có thể thêm vào đây nếu cần.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'webhook/stripe', // Uncomment if using webhooks
    ];
}
