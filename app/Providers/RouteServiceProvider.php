<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Đường dẫn tới route "home" của ứng dụng.
     *
     * Được dùng bởi Laravel để redirect user sau khi đăng nhập thành công (HOME).
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Namespace mặc định cho controller (tuỳ chọn).
     *
     * Nếu bật, các route controller sẽ tự prefix với namespace này.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Định nghĩa route model binding, pattern filter, rate limiting và group route.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Cấu hình rate limiting cho ứng dụng.
     *
     * - Ở đây đang giới hạn API 60 request/phút dựa trên user id hoặc IP.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
