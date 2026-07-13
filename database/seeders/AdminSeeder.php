<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
    'name' => 'Admin Boss',
    'email' => 'admin@sc.com',
    'password' => bcrypt('password123'),
    'phone_number' => '08123456789',
    'role' => 'admin',
]);
        //
    }
}
