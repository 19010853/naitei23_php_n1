<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Lấy danh sách pattern host được tin cậy.
     *
     * Mặc định trust toàn bộ subdomain của APP_URL (giúp xử lý proxy/nginx đúng header).
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
