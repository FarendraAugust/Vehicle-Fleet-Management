<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('regions')->insert([
            ['id'=>1,'name'=>'Head Office','type'=>'head_office','created_at'=>now(),'updated_at'=>now()],
            ['id'=>2,'name'=>'Branch Office','type'=>'branch','created_at'=>now(),'updated_at'=>now()],
            ['id'=>3,'name'=>'Mine A','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
            ['id'=>4,'name'=>'Mine B','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
            ['id'=>5,'name'=>'Mine C','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
            ['id'=>6,'name'=>'Mine D','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
            ['id'=>7,'name'=>'Mine E','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
            ['id'=>8,'name'=>'Mine F','type'=>'mine','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}