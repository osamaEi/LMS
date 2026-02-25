<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Create or update user ID 1 ───────────────────────────────
        $user = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name'               => 'Super Admin',
                'national_id'        => '1234567890',
                'password'           => Hash::make('password123'),
                'role'               => 'super_admin',
                'status'             => 'active',
                'email_verified_at'  => now(),
            ]
        );

        // Force role column to super_admin in case user already existed
        $user->update(['role' => 'super_admin']);

        // ── 2. Ensure super-admin Spatie role has ALL permissions ────────
        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());

        // ── 3. Assign the Spatie role to user (removes all other roles) ──
        $user->syncRoles(['super-admin']);

        // Reset cache after changes
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('✅ Super Admin جاهز:');
        $this->command->info("   الاسم    : {$user->name}");
        $this->command->info("   البريد   : {$user->email}");
        $this->command->info("   كلمة المرور: password123");
        $this->command->info("   الصلاحيات: {$role->permissions()->count()} صلاحية");
        $this->command->info('');
    }
}
