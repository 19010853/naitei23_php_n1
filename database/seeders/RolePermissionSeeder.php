<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Gán các quyền (permissions) cho từng role (Admin, Customer).
     *
     * - Admin: có toàn bộ quyền.
     * - Customer: chỉ có các quyền xem/đặt/hủy booking và xem tour.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $customerRole = Role::where('name', 'Customer')->first();

        if ($adminRole) {
            // Admin có tất cả quyền
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        if ($customerRole) {
            // Customer chỉ có quyền xem và đặt tour
            $customerPermissions = Permission::whereIn('name', [
                'view_tour',
                'create_booking',
                'view_booking',
                'cancel_booking',
            ])->get();
            $customerRole->permissions()->sync($customerPermissions->pluck('id'));
        }
    }
}

