<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('users')->insert([
            [
                'name' => 'Klaudia Weda',
                'email' => 'klaudia@admin.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Biasa',
                'email' => 'member@test.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('books')->insert([
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'stock' => 10, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Hasta Mitra', 'stock' => 5, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Kompas', 'stock' => 8, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'publisher' => 'KPG', 'stock' => 12, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Gramedia', 'stock' => 15, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
