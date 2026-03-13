<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicles')->insert([
            [
                'id' => 1,
                'image' => 'vehicles/01KKHXBKN3TNA9TV2HVDQA4NHD.jpg',
                'plate_number' => 'AG 1786 CA',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'year' => '2022',
                'vehicle_type' => 'goods',
                'ownership' => 'company',
                'region_id' => 3,
                'current_odometer' => 54,
                'status' => 'available'
            ]
        ]);
    }
}
