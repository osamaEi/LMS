<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Translate permission name to Arabic
     */
    public static function translatePermission(string $permission): string
    {
        $translations = [
            // Dashboard
            'view-dashboard' => 'عرض لوحة التحكم',

            // Users Management
            'view-users' => 'عرض المستخدمين',
            'create-users' => 'إنشاء مستخدمين',
            'edit-users' => 'تعديل المستخدمين',
            'delete-users' => 'حذف المستخدمين',

            // Sessions/Lessons Management
            'view-sessions' => 'عرض الدروس',
            'create-sessions' => 'إنشاء دروس',
            'edit-sessions' => 'تعديل الدروس',
            'delete-sessions' => 'حذف الدروس',

            // Subjects Management
            'view-subjects' => 'عرض المواد',
            'create-subjects' => 'إنشاء مواد',
            'edit-subjects' => 'تعديل المواد',
            'delete-subjects' => 'حذف المواد',

            // Programs Management
            'view-programs' => 'عرض المسارات التعليمية',
            'create-programs' => 'إنشاء مسارات تعليمية',
            'edit-programs' => 'تعديل المسارات التعليمية',
            'delete-programs' => 'حذف المسارات التعليمية',

            // Terms Management
            'view-terms' => 'عرض الفصول الدراسية',
            'create-terms' => 'إنشاء فصول دراسية',
            'edit-terms' => 'تعديل الفصول الدراسية',
            'delete-terms' => 'حذف الفصول الدراسية',

            // Enrollments
            'manage-enrollments' => 'إدارة التسجيلات',
            'view-enrollments' => 'عرض التسجيلات',

            // Attendance
            'manage-attendance' => 'إدارة الحضور',
            'view-attendance' => 'عرض الحضور',

            // Reports
            'view-reports' => 'عرض التقارير',
            'export-reports' => 'تصدير التقارير',
            'view-analytics' => 'عرض الإحصائيات',

            // Settings
            'view-settings' => 'عرض الإعدادات',
            'edit-settings' => 'تعديل الإعدادات',
            'manage-system' => 'إدارة النظام',

            // Roles & Permissions
            'manage-roles' => 'إدارة الأدوار',
            'manage-permissions' => 'إدارة الصلاحيات',

            // Surveys
            'view-surveys' => 'عرض الاستبيانات',
            'create-surveys' => 'إنشاء استبيانات',
            'edit-surveys' => 'تعديل الاستبيانات',
            'delete-surveys' => 'حذف الاستبيانات',

            // Tickets
            'view-tickets' => 'عرض التذاكر',
            'manage-tickets' => 'إدارة التذاكر',
            'assign-tickets' => 'تعيين التذاكر',

            // Teacher Ratings
            'view-ratings' => 'عرض التقييمات',
            'approve-ratings' => 'الموافقة على التقييمات',
        ];

        return $translations[$permission] ?? $permission;
    }

    /**
     * Get permissions grouped by category
     */
    public static function getGroupedPermissions($permissions)
    {
        $grouped = [
            'dashboard' => [
                'name' => 'لوحة التحكم',
                'permissions' => [],
            ],
            'users' => [
                'name' => 'إدارة المستخدمين',
                'permissions' => [],
            ],
            'sessions' => [
                'name' => 'الدروس والمحاضرات',
                'permissions' => [],
            ],
            'subjects' => [
                'name' => 'المواد الدراسية',
                'permissions' => [],
            ],
            'programs' => [
                'name' => 'المسارات التعليمية',
                'permissions' => [],
            ],
            'terms' => [
                'name' => 'الفصول الدراسية',
                'permissions' => [],
            ],
            'enrollments' => [
                'name' => 'التسجيلات',
                'permissions' => [],
            ],
            'attendance' => [
                'name' => 'الحضور والغياب',
                'permissions' => [],
            ],
            'reports' => [
                'name' => 'التقارير والإحصائيات',
                'permissions' => [],
            ],
            'surveys' => [
                'name' => 'الاستبيانات',
                'permissions' => [],
            ],
            'tickets' => [
                'name' => 'تذاكر الدعم',
                'permissions' => [],
            ],
            'ratings' => [
                'name' => 'تقييم المدربين',
                'permissions' => [],
            ],
            'roles' => [
                'name' => 'الأدوار والصلاحيات',
                'permissions' => [],
            ],
            'settings' => [
                'name' => 'الإعدادات',
                'permissions' => [],
            ],
        ];

        foreach ($permissions as $permission) {
            $name = $permission->name;

            // Categorize permissions
            if (str_contains($name, 'dashboard')) {
                $grouped['dashboard']['permissions'][] = $permission;
            } elseif (str_contains($name, 'user')) {
                $grouped['users']['permissions'][] = $permission;
            } elseif (str_contains($name, 'session')) {
                $grouped['sessions']['permissions'][] = $permission;
            } elseif (str_contains($name, 'subject')) {
                $grouped['subjects']['permissions'][] = $permission;
            } elseif (str_contains($name, 'program')) {
                $grouped['programs']['permissions'][] = $permission;
            } elseif (str_contains($name, 'term')) {
                $grouped['terms']['permissions'][] = $permission;
            } elseif (str_contains($name, 'enrollment')) {
                $grouped['enrollments']['permissions'][] = $permission;
            } elseif (str_contains($name, 'attendance')) {
                $grouped['attendance']['permissions'][] = $permission;
            } elseif (str_contains($name, 'report') || str_contains($name, 'analytics')) {
                $grouped['reports']['permissions'][] = $permission;
            } elseif (str_contains($name, 'survey')) {
                $grouped['surveys']['permissions'][] = $permission;
            } elseif (str_contains($name, 'ticket')) {
                $grouped['tickets']['permissions'][] = $permission;
            } elseif (str_contains($name, 'rating')) {
                $grouped['ratings']['permissions'][] = $permission;
            } elseif (str_contains($name, 'role') || str_contains($name, 'permission')) {
                $grouped['roles']['permissions'][] = $permission;
            } elseif (str_contains($name, 'setting') || str_contains($name, 'system')) {
                $grouped['settings']['permissions'][] = $permission;
            }
        }

        // Remove empty groups
        return array_filter($grouped, function($group) {
            return count($group['permissions']) > 0;
        });
    }

    /**
     * Get permission color class based on action
     */
    public static function getPermissionColor(string $permission): string
    {
        if (str_contains($permission, 'view')) {
            return 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
        } elseif (str_contains($permission, 'create')) {
            return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
        } elseif (str_contains($permission, 'edit') || str_contains($permission, 'manage')) {
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300';
        } elseif (str_contains($permission, 'delete')) {
            return 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
        } elseif (str_contains($permission, 'approve') || str_contains($permission, 'assign')) {
            return 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300';
        } elseif (str_contains($permission, 'export')) {
            return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300';
        }

        return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    }

    /**
     * Translate role name to Arabic
     */
    public static function translateRole(string $role): string
    {
        $translations = [
            'super-admin' => 'مدير عام',
            'admin' => 'مدير',
            'teacher' => 'معلم',
            'student' => 'طالب',
            'content-manager' => 'مدير محتوى',
            'support-agent' => 'موظف دعم',
            'supervisor' => 'مشرف',
            'coordinator' => 'منسق',
            'moderator' => 'مشرف',
            'editor' => 'محرر',
            'viewer' => 'مشاهد',
            'guest' => 'ضيف',
        ];

        return $translations[$role] ?? $role;
    }

    /**
     * Get role color class based on role name
     */
    public static function getRoleColor(string $role): string
    {
        return match(strtolower($role)) {
            'super-admin', 'super_admin' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            'admin' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
            'teacher' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'student' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            'content-manager', 'content_manager' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'support-agent', 'support_agent' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            'supervisor' => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
            'coordinator' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300',
            'moderator' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
            'editor' => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
