<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Danh sách các proxy được tin cậy cho ứng dụng.
     *
     * Có thể cấu hình IP/ CIDR nếu deploy sau reverse proxy (Nginx, Load Balancer...).
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Các header được dùng để phát hiện thông tin proxy (IP thật của client).
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
