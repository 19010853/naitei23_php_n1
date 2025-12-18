<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed danh sách quyền (permissions) cơ bản cho hệ thống.
     *
     * - Các quyền sẽ được gắn cho role ở RolePermissionSeeder.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'create_tour'],
            ['name' => 'edit_tour'],
            ['name' => 'delete_tour'],
            ['name' => 'view_tour'],
            ['name' => 'create_booking'],
            ['name' => 'cancel_booking'],
            ['name' => 'view_booking'],
            ['name' => 'manage_users'],
            ['name' => 'manage_roles'],
            ['name' => 'manage_permissions'],
        ];

        Permission::insert($permissions);
    }
}

