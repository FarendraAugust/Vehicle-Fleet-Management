<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriversSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('drivers')->insert([
            [
                'id' => 1,
                'image' => 'drivers/01KKHGS9GEK1NYKBCQZ7PDZ703.png',
                'name' => 'Ridho',
                'phone' => '081337232587',
                'license_number' => '19261781615762',
                'status' => 'available',
                'active' => 1
            ],
            [
                'id' => 2,
                'image' => 'drivers/01KKHNZR352E496TT3WVTDCSJ0.png',
                'name' => 'Hernando',
                'phone' => '8672881721121',
                'license_number' => '18979712937',
                'status' => 'available',
                'active' => 1
            ]
        ]);
    }
}
