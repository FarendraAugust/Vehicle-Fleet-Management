<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bookings')->insert([
            [
                'id' => 2,
                'vehicle_id' => 1,
                'driver_id' => 1,
                'requested_by' => 1,
                'destination' => 'Mining A',
                'purpose' => 'ksnka',
                'start_date' => '2026-03-14 05:00:00',
                'end_date' => '2026-03-21 13:00:00',
                'status' => 'completed'
            ]
        ]);
    }
}
