<?php

namespace Database\Seeders;

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
            RolesAndPermissionsSeeder::class,

            // 2. Settings
            SettingsSeeder::class,

            // 3. Demo Data (Users, Programs, Terms, Subjects, Enrollments)
            DashboardDemoSeeder::class,
        ]);
    }
}
