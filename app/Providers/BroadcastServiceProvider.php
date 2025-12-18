<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Khởi động các dịch vụ broadcast (Realtime events).
     *
     * - Đăng ký route cho kênh broadcast.
     * - Load file định nghĩa channels (routes/channels.php).
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
