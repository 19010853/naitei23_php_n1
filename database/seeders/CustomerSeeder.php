<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerRole = Role::where('name', 'Customer')->first();

        if (!$customerRole) {
            $this->command->warn('Customer role not found. Run RoleSeeder first.');
            return;
        }

        $customers = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lê Văn Cường',
                'email' => 'levancuong@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Phạm Thị Dung',
                'email' => 'phamthidung@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hoàng Văn Em',
                'email' => 'hoangvanem@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($customers as $customerData) {
            $user = User::updateOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );

            // Attach customer role if not already attached
            if (!$user->roles()->where('role_id', $customerRole->id)->exists()) {
                $user->roles()->attach($customerRole->id);
            }
        }

        $this->command->info('Created ' . count($customers) . ' customer users.');
    }
}

