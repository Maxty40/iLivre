<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web']
        );

        $member = Role::firstOrCreate(
            ['name' => 'Member', 'guard_name' => 'web']
        );

        $this->command->info("Role 'Admin' ID: {$admin->id}");
        $this->command->info("Role 'Member' ID: {$member->id}");
    }
}
