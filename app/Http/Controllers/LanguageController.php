<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Persist the user preferred language in the session.
     *
     * - Chỉ cho phép các locale trong danh sách hỗ trợ.
     * - Lưu vào session để áp dụng cho các request tiếp theo.
     */
    public function changeLanguage(Request $request, string $locale): RedirectResponse
    {
        $supported = ['en', 'vi', 'ja'];
        $selectedLocale = in_array($locale, $supported, true) ? $locale : config('app.locale');

        Session::put('locale', $selectedLocale);
        App::setLocale($selectedLocale);

        return redirect()->back();
    }
}
