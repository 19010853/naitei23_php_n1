<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Danh sách tên cookie sẽ KHÔNG bị mã hoá.
     *
     * Mặc định để trống, tức là tất cả cookie đều được encrypt để tăng bảo mật.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
