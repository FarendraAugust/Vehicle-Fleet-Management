<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_booking',
            'view_any_booking',
            'create_booking',
            'update_booking',
            'restore_booking',
            'restore_any_booking',
            'replicate_booking',
            'reorder_booking',
            'delete_booking',
            'delete_any_booking',
            'force_delete_booking',
            'force_delete_any_booking',

            'view_driver',
            'view_any_driver',
            'create_driver',
            'update_driver',
            'restore_driver',
            'restore_any_driver',
            'replicate_driver',
            'reorder_driver',
            'delete_driver',
            'delete_any_driver',
            'force_delete_driver',
            'force_delete_any_driver',

            'view_fuel::log',
            'view_any_fuel::log',
            'create_fuel::log',
            'update_fuel::log',
            'restore_fuel::log',
            'restore_any_fuel::log',
            'replicate_fuel::log',
            'reorder_fuel::log',
            'delete_fuel::log',
            'delete_any_fuel::log',
            'force_delete_fuel::log',
            'force_delete_any_fuel::log',

            'view_region',
            'view_any_region',
            'create_region',
            'update_region',
            'restore_region',
            'restore_any_region',
            'replicate_region',
            'reorder_region',
            'delete_region',
            'delete_any_region',
            'force_delete_region',
            'force_delete_any_region',

            'view_service::log',
            'view_any_service::log',
            'create_service::log',
            'update_service::log',
            'restore_service::log',
            'restore_any_service::log',
            'replicate_service::log',
            'reorder_service::log',
            'delete_service::log',
            'delete_any_service::log',
            'force_delete_service::log',
            'force_delete_any_service::log',

            'view_vehicle',
            'view_any_vehicle',
            'create_vehicle',
            'update_vehicle',
            'restore_vehicle',
            'restore_any_vehicle',
            'replicate_vehicle',
            'reorder_vehicle',
            'delete_vehicle',
            'delete_any_vehicle',
            'force_delete_vehicle',
            'force_delete_any_vehicle',

            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',

            'view_activity::log',
            'view_any_activity::log',
            'create_activity::log',
            'update_activity::log',
            'restore_activity::log',
            'restore_any_activity::log',
            'replicate_activity::log',
            'reorder_activity::log',
            'delete_activity::log',
            'delete_any_activity::log',
            'force_delete_activity::log',
            'force_delete_any_activity::log',

            'view_approval',
            'view_any_approval',
            'create_approval',
            'update_approval',
            'restore_approval',
            'restore_any_approval',
            'replicate_approval',
            'reorder_approval',
            'delete_approval',
            'delete_any_approval',
            'force_delete_approval',
            'force_delete_any_approval',

            'view_vehicle::booking::report',
            'view_any_vehicle::booking::report',
            'create_vehicle::booking::report',
            'update_vehicle::booking::report',
            'restore_vehicle::booking::report',
            'restore_any_vehicle::booking::report',
            'replicate_vehicle::booking::report',
            'reorder_vehicle::booking::report',
            'delete_vehicle::booking::report',
            'delete_any_vehicle::booking::report',
            'force_delete_vehicle::booking::report',
            'force_delete_any_vehicle::booking::report',

            'view_vehicle::usage',
            'view_any_vehicle::usage',
            'create_vehicle::usage',
            'update_vehicle::usage',
            'restore_vehicle::usage',
            'restore_any_vehicle::usage',
            'replicate_vehicle::usage',
            'reorder_vehicle::usage',
            'delete_vehicle::usage',
            'delete_any_vehicle::usage',
            'force_delete_vehicle::usage',
            'force_delete_any_vehicle::usage',

            'widget_VehicleUsageChart',
            'widget_FuelUsageChart',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}