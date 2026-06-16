<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Call modular seeders first to establish Spatie Roles and Permissions
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // 2. Insert initial users without the deprecated 'role_id' column
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Klaudia Weda',
                'email' => 'klaudia@admin.com',
                'password' => Hash::make('password'),
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Member Biasa',
                'email' => 'member@test.com',
                'password' => Hash::make('password'),
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Fetch instantiated roles from Spatie DB state
        $adminRole = Role::where('name', 'Admin')->first();
        $memberRole = Role::where('name', 'Member')->first();

        // 4. Attach roles to users using Spatie polymorph table
        DB::table('model_has_roles')->insert([
            [
                'role_id' => $adminRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
            ],
            [
                'role_id' => $memberRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ],
        ]);

        // 5. Seed books catalog
        DB::table('books')->insert([
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'stock' => 10, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Hasta Mitra', 'stock' => 5, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Kompas', 'stock' => 8, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'publisher' => 'KPG', 'stock' => 12, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Gramedia', 'stock' => 15, 'cover_image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
