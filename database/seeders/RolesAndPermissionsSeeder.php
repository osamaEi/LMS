<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Users Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Courses Management
            'view-courses',
            'create-courses',
            'edit-courses',
            'delete-courses',

            // Sessions/Lessons Management
            'view-sessions',
            'create-sessions',
            'edit-sessions',
            'delete-sessions',

            // Subjects Management
            'view-subjects',
            'create-subjects',
            'edit-subjects',
            'delete-subjects',

            // Programs Management
            'view-programs',
            'create-programs',
            'edit-programs',
            'delete-programs',

            // Terms Management
            'view-terms',
            'create-terms',
            'edit-terms',
            'delete-terms',

            // Enrollments
            'manage-enrollments',
            'view-enrollments',

            // Attendance
            'manage-attendance',
            'view-attendance',

            // Reports
            'view-reports',
            'export-reports',
            'view-analytics',

            // Settings
            'view-settings',
            'edit-settings',
            'manage-system',

            // Roles & Permissions
            'manage-roles',
            'manage-permissions',

            // Surveys
            'view-surveys',
            'create-surveys',
            'edit-surveys',
            'delete-surveys',

            // Tickets
            'view-tickets',
            'manage-tickets',
            'assign-tickets',

            // Teacher Ratings
            'view-ratings',
            'approve-ratings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin - has most permissions except system management
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'view-dashboard',
            'view-users', 'create-users', 'edit-users',
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses',
            'view-sessions', 'create-sessions', 'edit-sessions', 'delete-sessions',
            'view-subjects', 'create-subjects', 'edit-subjects', 'delete-subjects',
            'view-programs', 'create-programs', 'edit-programs', 'delete-programs',
            'view-terms', 'create-terms', 'edit-terms', 'delete-terms',
            'manage-enrollments', 'view-enrollments',
            'manage-attendance', 'view-attendance',
            'view-reports', 'export-reports', 'view-analytics',
            'view-surveys', 'create-surveys', 'edit-surveys', 'delete-surveys',
            'view-tickets', 'manage-tickets', 'assign-tickets',
            'view-ratings', 'approve-ratings',
        ]);

        // Teacher - limited permissions
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacherRole->givePermissionTo([
            'view-dashboard',
            'view-courses',
            'view-sessions', 'create-sessions', 'edit-sessions',
            'view-subjects',
            'view-enrollments',
            'manage-attendance', 'view-attendance',
            'view-reports',
            'view-surveys',
            'view-tickets',
        ]);

        // Student - very limited permissions
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $studentRole->givePermissionTo([
            'view-dashboard',
            'view-courses',
            'view-sessions',
            'view-subjects',
            'view-attendance',
            'view-surveys',
            'view-tickets',
        ]);

        // Content Manager
        $contentManagerRole = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'web']);
        $contentManagerRole->givePermissionTo([
            'view-dashboard',
            'view-courses', 'create-courses', 'edit-courses',
            'view-sessions', 'create-sessions', 'edit-sessions',
            'view-subjects', 'create-subjects', 'edit-subjects',
            'view-programs', 'edit-programs',
        ]);

        // Support Agent
        $supportRole = Role::firstOrCreate(['name' => 'support-agent', 'guard_name' => 'web']);
        $supportRole->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-tickets', 'manage-tickets',
            'view-surveys',
        ]);
    }
}
