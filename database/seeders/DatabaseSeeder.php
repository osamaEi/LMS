<?php

namespace Database\Seeders;

use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles and Permissions (must be first)
            PermissionSeeder::class,

            // 2. Super Admin user with all permissions
            SuperAdminSeeder::class,
        ]);
    }
}
