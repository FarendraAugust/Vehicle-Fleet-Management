<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleHasPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🔥 APPROVER (PERSIS SESUAI DATA LAMA)
        $permissionIds = [
            ...range(25, 36),
            ...range(49, 60),
            ...range(91, 102),
            129,
            130
        ];

        $permissionAdminExcept = [
            ...range(91, 102)
        ];

        // ambil NAME dari ID (biar aman untuk Shield)
        $permissionNames = Permission::whereIn('id', $permissionIds)
            ->pluck('name')
            ->toArray();

        $permissionNamesAdmin = Permission::whereIn('id', $permissionAdminExcept)
            ->pluck('name')
            ->toArray();

        $approverPermissions = Permission::whereIn('name', $permissionNames)->get();

        // 🔥 SUPER ADMIN = FULL ACCESS
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin?->syncPermissions(Permission::all());

        // 🔥 ADMIN = SEMUA KECUALI APPROVAL
        $adminPermissions = Permission::whereNotIn('name', $permissionNamesAdmin)->get();

        $admin = Role::where('name', 'admin')->first();
        $admin?->syncPermissions($adminPermissions);

        // 🔥 APPROVER
        $approver = Role::where('name', 'approver')->first();
        $approver?->syncPermissions($approverPermissions);
    }
}
