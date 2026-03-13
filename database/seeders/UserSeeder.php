<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Budi Santoso',
            'Andi Wijaya',
            'Rudi Hartono',
            'Siti Aminah',
            'Dewi Lestari',
            'Agus Saputra',
            'Rina Marlina',
            'Fajar Nugroho',
            'Tono Prasetyo',
            'Maya Putri'
        ];

        foreach ($names as $name) {
            User::create([
                'name' => $name,
                'email' => Str::slug($name) . '@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
        }
    }
    
}
