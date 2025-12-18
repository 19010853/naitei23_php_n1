<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed tài khoản admin và một tài khoản customer mẫu.
     *
     * - Gắn role tương ứng cho từng user (Admin / Customer).
     *
     * @return void
     */
    public function run()
    {
        // Tạo admin user
        $admin = User::create([
            'name' => 'Admin Account',
            'email' => 'admin.account@sun-asterisk.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Gán role Admin cho user (many-to-many relationship)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->roles()->attach($adminRole->id);
        }

        // Tạo customer user mẫu
        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Gán role Customer cho user
        $customerRole = Role::where('name', 'Customer')->first();
        if ($customerRole) {
            $customer->roles()->attach($customerRole->id);
        }
    }
}

