<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping giữa model và policy tương ứng.
     *
     * - Ở đây đang map Payment -> PaymentPolicy để kiểm soát quyền xem/tải invoice.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Payment::class => \App\Policies\PaymentPolicy::class,
    ];

    /**
     * Đăng ký các service liên quan đến authentication / authorization.
     *
     * - Gọi registerPolicies() để Laravel biết dùng policy nào cho model tương ứng.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
