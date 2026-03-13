<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleUsagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_usages')->insert([
            [
                'id' => 1,
                'vehicle_id' => 1,
                'booking_id' => 2,
                'start_odometer' => 0,
                'end_odometer' => 87
            ],
            [
                'id' => 2,
                'vehicle_id' => 1,
                'booking_id' => 2,
                'start_odometer' => 87,
                'end_odometer' => 54
            ]
        ]);
    }
}
