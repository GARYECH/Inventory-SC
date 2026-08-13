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
        // 1. Buat Akun Admin (Sesuai request)
        User::create([
            'name' => 'Admin Inventory SC',
            'email' => 'admin@sc.com',
            'password' => Hash::make('adminsc123'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Mahasiswa (Testing)
        User::create([
            'name' => 'Gregory Edgard Christian',
            'email' => 'student@ciputra.ac.id',
            'password' => Hash::make('password'), // Password: password
            'role' => 'student',
        ]);
    }
}