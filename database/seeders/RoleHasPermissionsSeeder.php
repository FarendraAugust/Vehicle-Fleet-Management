<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleHasPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 reset cache dulu
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::all();

        // 🔥 ROLE 1 & 2 = FULL ACCESS
        foreach ([1, 2] as $roleId) {
            $role = Role::find($roleId);
            $role?->syncPermissions($permissions);
        }

        // 🔥 ROLE 3 = CUSTOM (sesuai data lo)
        $role3Permissions = Permission::whereIn('id', [
            25,26,27,28,29,30,31,32,33,34,35,36,
            49,50,51,52,53,54,55,56,57,58,59,60,
            91,92,93,94,95,96,97,98,99,100,101,102,
            129,130
        ])->get();

        $role3 = Role::find(3);
        $role3?->syncPermissions($role3Permissions);
    }
}