<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserBankAccount;
use Illuminate\Database\Seeder;

class UserBankAccountSeeder extends Seeder
{
    /**
     * Seed một số tài khoản ngân hàng demo cho UI payout/payment.
     *
     * - Áp dụng cho các user key (admin/account & customer@example.com).
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereIn('email', [
            'admin.account@sun-asterisk.com',
            'customer@example.com',
        ])->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found for UserBankAccountSeeder.');
            return;
        }

        foreach ($users as $user) {
            UserBankAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'account_number' => '9704' . str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
                ],
                [
                    'bank_name' => 'TPBank',
                    'account_name' => $user->name,
                    'is_default' => true,
                ]
            );

            UserBankAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'account_number' => '9700' . str_pad((string) ($user->id + 10), 8, '0', STR_PAD_LEFT),
                ],
                [
                    'bank_name' => 'Vietcombank',
                    'account_name' => $user->name,
                    'is_default' => false,
                ]
            );
        }
    }
}


