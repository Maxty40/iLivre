<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        // 2 Admin, 18 Users = Total 20 Users
        $users = [
            ['name' => 'Klaudia Weda', 'email' => 'klaudia@email.com', 'role_id' => 1],
            ['name' => 'Yudo Prasetio', 'email' => 'yudo@email.com', 'role_id' => 1],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@email.com', 'role_id' => 2],
            ['name' => 'Siti Aminah', 'email' => 'siti@email.com', 'role_id' => 2],
            ['name' => 'Budi Santoso', 'email' => 'budi@email.com', 'role_id' => 2],
            ['name' => 'Citra Dewi', 'email' => 'citra@email.com', 'role_id' => 2],
            ['name' => 'Dhiauddin Arfa', 'email' => 'dhia@email.com', 'role_id' => 2],
            ['name' => 'Ulwan Luthfi', 'email' => 'ulwan@email.com', 'role_id' => 2],
            ['name' => 'Ahmad Afifi', 'email' => 'afifi@email.com', 'role_id' => 2],
            ['name' => 'Freeze Ad Kaban', 'email' => 'freeze@email.com', 'role_id' => 2],
            ['name' => 'Budiyono Siregar', 'email' => 'budiyono@email.com', 'role_id' => 2],
            ['name' => 'Bilqis Salsabila', 'email' => 'bilqis@email.com', 'role_id' => 2],
            ['name' => 'Bunga Lestari', 'email' => 'bunga@email.com', 'role_id' => 2],
            ['name' => 'Amy Adinanta', 'email' => 'amy@email.com', 'role_id' => 2],
            ['name' => 'Xavier Wijoyo', 'email' => 'xavier@email.com', 'role_id' => 2],
            ['name' => 'Jessica Sudarsono', 'email' => 'jessica@email.com', 'role_id' => 2],
            ['name' => 'Michael Alviono', 'email' => 'michael@email.com', 'role_id' => 2],
            ['name' => 'Asep Surasep', 'email' => 'asep@email.com', 'role_id' => 2],
            ['name' => 'Surti Sumiati', 'email' => 'surti@email.com', 'role_id' => 2],
            ['name' => 'Munaroh', 'email' => 'munaroh@email.com', 'role_id' => 2],
        ];

        foreach ($users as $index => $user) {
            $userId = DB::table('users')->insertGetId([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $password,
                'role_id' => $user['role_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Requirement 2 & 3: Seed 1:1 Membership Card directly for each user
            DB::table('membership_cards')->insert([
                'card_number' => 'KRT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'issued_date' => now()->subMonths(5),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
