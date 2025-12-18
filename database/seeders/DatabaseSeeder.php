<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed toàn bộ dữ liệu demo cho ứng dụng.
     *
     * - Gọi lần lượt các seeder: roles, permissions, users, tours, bookings, engagements...
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            UserBankAccountSeeder::class,
            CategorySeeder::class,
            TourSeeder::class,
            BookingSeeder::class,
            EngagementSeeder::class,
        ]);
    }
}
