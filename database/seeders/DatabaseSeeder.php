<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(RegionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RoleHasPermissionsSeeder::class);
        $this->call(ModelHasRolesSeeder::class);
        $this->call(DriversSeeder::class);
        $this->call(VehiclesSeeder::class);
        $this->call(BookingsSeeder::class);
        $this->call(ApprovalsSeeder::class);
        $this->call(VehicleUsagesSeeder::class);
    }
}
