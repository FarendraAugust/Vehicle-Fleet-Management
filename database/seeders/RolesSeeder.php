<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id'=>1,'name'=>'super_admin','guard_name'=>'web'],
            ['id'=>2,'name'=>'admin','guard_name'=>'web'],
            ['id'=>3,'name'=>'approver','guard_name'=>'web'],
        ]);
    }
}