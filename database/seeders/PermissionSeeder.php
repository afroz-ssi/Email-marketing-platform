<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "Dashboard",
            "User",
            "Lead",
            "Report",
            'Campaigns',
            "OutlookAccount",

        ];
        
        foreach ($permissions as $permission) {
            $allPermission = Permission::create(['name' => $permission, 'guard_name' => 'web']);
            $admin = Role::where('name', 'SUPER-ADMIN')->first();
            $role = new RoleHasPermission();
            $role->permission_id = $allPermission->id;
            $role->role_id = $admin->id;
            $role->timestamps = false;
            $role->save();
        }
    }
}
