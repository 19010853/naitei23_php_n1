<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
