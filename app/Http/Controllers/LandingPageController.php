<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Trang landing cơ bản (hiện tại đang dùng chung view 'welcome').
     *
     * Có thể mở rộng thêm logic thống kê / hiển thị banner trong tương lai.
     */
    public function landingPage(Request $request)
    {
        return view('welcome');
    }
}
