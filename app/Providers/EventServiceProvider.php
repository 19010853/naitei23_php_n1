<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapping giữa event và listener tương ứng.
     *
     * Ví dụ: khi Registered fired, sẽ gửi email verify thông qua SendEmailVerificationNotification.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Đăng ký các event cho ứng dụng.
     *
     * Có thể dùng để thêm event/listener động hoặc ghi log khi boot.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
