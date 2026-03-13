<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('approvals')->insert([
            [
                'id' => 1,
                'booking_id' => 2,
                'approver_id' => 3,
                'level' => 1,
                'status' => 'approved'
            ],
            [
                'id' => 2,
                'booking_id' => 2,
                'approver_id' => 7,
                'level' => 2,
                'status' => 'approved'
            ]
        ]);
    }
}
