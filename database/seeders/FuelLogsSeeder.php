<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fuel_logs')->insert([
            [
                'id' => 1,
                'vehicle_id' => 1,
                'date' => '2026-03-19',
                'fuel_amount' => 20,
                'cost' => 200000
            ]
        ]);
    }
}
