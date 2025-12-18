<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Đăng ký các kênh broadcast mà ứng dụng hỗ trợ.
| Hàm callback dùng để kiểm tra user hiện tại có quyền lắng nghe (subscribe)
| vào channel tương ứng hay không.
|
*/

// Kênh riêng cho từng user, chỉ cho phép nếu id trong URL trùng với id user hiện tại.
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
