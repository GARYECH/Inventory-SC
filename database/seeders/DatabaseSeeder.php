<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat atau update akun Admin
        User::updateOrCreate(
            [
                'email' => 'admin@sc.com',
            ],
            [
                'name' => 'Admin Inventory SC',
                'password' => Hash::make('adminsc123'),
                'role' => 'admin',
            ]
        );

        // 2. Buat atau update akun Mahasiswa
        User::updateOrCreate(
            [
                'email' => 'student@ciputra.ac.id',
            ],
            [
                'name' => 'Gregory Edgard Christian',
                'password' => Hash::make('password'),
                'role' => 'student',
            ]
        );
    }
}