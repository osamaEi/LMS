<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Super Admin',
                'national_id' => '1234567890',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@lms.com');
        $this->command->info('National ID: 1234567890');
        $this->command->info('Password: password123');
    }
}
