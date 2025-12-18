<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Định nghĩa các lệnh Artisan dạng Closure.
| Mỗi Closure được bind với instance command, cho phép tương tác IO đơn giản.
|
*/

// Lệnh ví dụ: in ra một câu quote truyền cảm hứng.
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
