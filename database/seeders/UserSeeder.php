<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'javohir8386@gmail.com',
        ], [
            'role' => 'admin',
            'password' => Hash::make('Javohir03'),
            'To‘liq_ismi' => 'BAXTIYORJONOV MUHAMMADJAVOXIR JAMSHIDJON O‘G‘LI',
        ]);
        User::updateOrCreate([
            'email' => 'samiyusuf@gmail.com',
        ], [
            'role' => 'admin',
            'password' => Hash::make('yusuf95'),
            'To‘liq_ismi' => 'IKRAMOV MUHAMMAD-YUSUF ABDULAXAD O‘G‘LI',
        ]);
    }
}
