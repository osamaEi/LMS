<?php

namespace Database\Seeders;

use Database\Seeders\FaqSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SubjectsSeeder;
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

            // 2. Super Admin user with all permissions
           // SubjectsSeeder::class,

            // 3. FAQ content
            FaqSeeder::class,
        ]);
    }
}
