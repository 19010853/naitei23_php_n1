<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các service / binding dùng chung cho toàn ứng dụng.
     *
     * Hiện tại chưa sử dụng, có thể thêm binding hoặc singleton tại đây nếu cần.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Khởi động (bootstrap) các thiết lập ứng dụng.
     *
     * Ví dụ: có thể cấu hình view composer, macro, hoặc config động tại đây.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
