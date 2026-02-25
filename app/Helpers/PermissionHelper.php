<?php

namespace App\Helpers;

use Database\Seeders\PermissionSeeder;

class PermissionHelper
{
    /**
     * Translate a permission name to Arabic.
     * Source of truth is PermissionSeeder::$permissions.
     */
    public static function translatePermission(string $permission): string
    {
        return PermissionSeeder::$permissions[$permission] ?? $permission;
    }

    /**
     * Group definitions – each key maps to a list of permission name substrings.
     */
    private static array $groups = [
        'dashboard'            => ['name' => 'لوحة التحكم',                    'match' => ['dashboard']],
        'users'                => ['name' => 'إدارة المستخدمين',                'match' => ['user']],
        'teachers'             => ['name' => 'إدارة المعلمين',                  'match' => ['teacher']],
        'students'             => ['name' => 'إدارة الطلاب',                    'match' => ['student']],
        'programs'             => ['name' => 'المسارات التعليمية',              'match' => ['program']],
        'terms'                => ['name' => 'الفصول الدراسية',                'match' => ['term']],
        'subjects'             => ['name' => 'المواد الدراسية',                 'match' => ['subject']],
        'sessions'             => ['name' => 'الجلسات والمحاضرات',              'match' => ['session', 'zoom']],
        'recordings'           => ['name' => 'التسجيلات',                       'match' => ['recording']],
        'enrollments'          => ['name' => 'التسجيل والقبول',                 'match' => ['enrollment']],
        'attendance'           => ['name' => 'الحضور والغياب',                  'match' => ['attendance']],
        'quizzes'              => ['name' => 'الاختبارات',                      'match' => ['quiz']],
        'payments'             => ['name' => 'المدفوعات والرسوم',               'match' => ['payment', 'installment', 'waive']],
        'reports'              => ['name' => 'التقارير والإحصائيات',            'match' => ['report', 'analytics', 'nelc', 'progress', 'grade-report', 'teacher-performance']],
        'surveys'              => ['name' => 'الاستبيانات',                     'match' => ['survey']],
        'tickets'              => ['name' => 'تذاكر الدعم الفني',              'match' => ['ticket']],
        'ratings'              => ['name' => 'تقييم المعلمين',                  'match' => ['rating']],
        'pending_users'        => ['name' => 'طلبات التسجيل المعلقة',          'match' => ['pending-user']],
        'activity_logs'        => ['name' => 'سجل النشاطات',                   'match' => ['activity-log']],
        'roles'                => ['name' => 'الأدوار والصلاحيات',             'match' => ['role', 'permission']],
        'settings'             => ['name' => 'الإعدادات والنظام',              'match' => ['setting', 'system', 'xapi']],
    ];

    /**
     * Get permissions grouped by category for display in views.
     */
    public static function getGroupedPermissions($permissions): array
    {
        // Build skeleton
        $grouped = [];
        foreach (self::$groups as $key => $meta) {
            $grouped[$key] = ['name' => $meta['name'], 'permissions' => []];
        }
        $grouped['other'] = ['name' => 'أخرى', 'permissions' => []];

        foreach ($permissions as $permission) {
            $placed = false;
            $name   = $permission->name;

            foreach (self::$groups as $key => $meta) {
                foreach ($meta['match'] as $keyword) {
                    if (str_contains($name, $keyword)) {
                        $grouped[$key]['permissions'][] = $permission;
                        $placed = true;
                        break 2;
                    }
                }
            }

            if (!$placed) {
                $grouped['other']['permissions'][] = $permission;
            }
        }

        // Remove empty groups
        return array_filter($grouped, fn($g) => count($g['permissions']) > 0);
    }

    /**
     * Tailwind color class based on action type.
     */
    public static function getPermissionColor(string $permission): string
    {
        if (str_contains($permission, 'view')) {
            return 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
        } elseif (str_contains($permission, 'create') || str_contains($permission, 'add')) {
            return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
        } elseif (str_contains($permission, 'edit') || str_contains($permission, 'manage') || str_contains($permission, 'sync') || str_contains($permission, 'toggle')) {
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300';
        } elseif (str_contains($permission, 'delete') || str_contains($permission, 'cancel') || str_contains($permission, 'remove') || str_contains($permission, 'reject')) {
            return 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
        } elseif (str_contains($permission, 'approve') || str_contains($permission, 'assign') || str_contains($permission, 'waive') || str_contains($permission, 'grade')) {
            return 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300';
        } elseif (str_contains($permission, 'export') || str_contains($permission, 'record')) {
            return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300';
        }

        return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    }

    /**
     * Translate role name to Arabic.
     */
    public static function translateRole(string $role): string
    {
        $translations = [
            'super-admin'     => 'مدير عام',
            'super_admin'     => 'مدير عام',
            'admin'           => 'مدير',
            'teacher'         => 'معلم',
            'student'         => 'طالب',
            'content-manager' => 'مدير محتوى',
            'content_manager' => 'مدير محتوى',
            'support-agent'   => 'موظف دعم',
            'support_agent'   => 'موظف دعم',
            'supervisor'      => 'مشرف',
            'coordinator'     => 'منسق',
            'moderator'       => 'مشرف محتوى',
            'editor'          => 'محرر',
            'viewer'          => 'مشاهد',
            'guest'           => 'ضيف',
        ];

        return $translations[$role] ?? $role;
    }

    /**
     * Tailwind color class for a role badge.
     */
    public static function getRoleColor(string $role): string
    {
        return match (strtolower($role)) {
            'super-admin', 'super_admin' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'admin'                       => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
            'teacher'                     => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'student'                     => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            'content-manager',
            'content_manager'             => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'support-agent',
            'support_agent'               => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'supervisor'                  => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
            'coordinator'                 => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300',
            'moderator'                   => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
            'editor'                      => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300',
            default                       => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
