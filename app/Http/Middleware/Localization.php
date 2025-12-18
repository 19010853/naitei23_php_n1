<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Middleware thiết lập ngôn ngữ (locale) cho mỗi request.
     *
     * - Lấy locale từ session ('locale' hoặc 'lang'), fallback về config('app.locale').
     * - Gọi App::setLocale() để áp dụng cho toàn bộ request hiện tại.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', Session::get('lang', config('app.locale')));
        App::setLocale($locale);

        return $next($request);
    }
}
