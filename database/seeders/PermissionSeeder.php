<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * All system permissions with Arabic display names.
     * Structure: 'permission-name' => 'الاسم بالعربي'
     */
    public static array $permissions = [

        // ─── لوحة التحكم ───────────────────────────────
        'view-dashboard'                 => 'عرض لوحة التحكم',

        // ─── المستخدمون ────────────────────────────────
        'view-users'                     => 'عرض المستخدمين',
        'create-users'                   => 'إضافة مستخدمين',
        'edit-users'                     => 'تعديل المستخدمين',
        'delete-users'                   => 'حذف المستخدمين',
        'toggle-user-status'             => 'تفعيل / تعطيل حسابات المستخدمين',

        // ─── المدربون──────────────────────────────────
        'view-teachers'                  => 'عرض المدربون ',
        'create-teachers'                => 'إضافة  مدربين',
        'edit-teachers'                  => 'تعديل بيانات المدربون ',
        'delete-teachers'                => 'حذف المدربون ',

        // ───  المتدربون  ────────────────────────────────────
        'view-students'                  => 'عرض  المتدربون ',
        'create-students'                => 'إضافة طلاب',
        'edit-students'                  => 'تعديل بيانات  المتدربون ',
        'delete-students'                => 'حذف  المتدربون ',
        'assign-program-to-student'      => 'تعيين دبلوم تدريبي للطالب',
        'remove-program-from-student'    => 'إزالة الدبلوم التدريبي من المتدرب ',
        'toggle-student-status'          => 'تفعيل / تعطيل حساب المتدرب ',

        // ─── الدبلومات    التعليمية (البرامج) ─────────────
        'view-programs'                  => 'عرض الدبلومات    التعليمية',
        'create-programs'                => 'إضافة دبلومات   تعليمية',
        'edit-programs'                  => 'تعديل الدبلومات    التعليمية',
        'delete-programs'                => 'حذف الدبلومات    التعليمية',

        // ─── الفصول التدريبية  ───────────────────────────
        'view-terms'                     => 'عرض الفصول التدريبية ',
        'create-terms'                   => 'إضافة فصول دراسية',
        'edit-terms'                     => 'تعديل الفصول التدريبية ',
        'delete-terms'                   => 'حذف الفصول التدريبية ',

        // ─── المقررات  التدريبية  ───────────────────────────
        'view-subjects'                  => 'عرض المقررات  التدريبية ',
        'create-subjects'                => 'إضافة مقرارت دراسية',
        'edit-subjects'                  => 'تعديل المقررات  التدريبية ',
        'delete-subjects'                => 'حذف المقررات  التدريبية ',

        // ─── الدروس والجلسات ───────────────────────────
        'view-sessions'                  => 'عرض الجلسات والمحاضرات',
        'create-sessions'                => 'إضافة جلسات ومحاضرات',
        'edit-sessions'                  => 'تعديل الجلسات والمحاضرات',
        'delete-sessions'                => 'حذف الجلسات والمحاضرات',
        'manage-zoom'                    => 'إدارة اجتماعات Zoom',

        // ─── التسجيلات (Recordings) ────────────────────
        'view-recordings'                => 'عرض التسجيلات',
        'manage-recordings'              => 'إدارة التسجيلات',
        'sync-recordings'                => 'مزامنة التسجيلات من Zoom',
        'delete-recordings'              => 'حذف التسجيلات',

        // ─── التسجيل والقبول ───────────────────────────
        'view-enrollments'               => 'عرض التسجيلات',
        'manage-enrollments'             => 'إدارة التسجيلات',
        'manage-program-enrollments'     => 'إدارة طلبات الالتحاق بالدبلومات   ',
        'approve-program-enrollments'    => 'قبول طلبات الالتحاق بالدبلومات   ',
        'reject-program-enrollments'     => 'رفض طلبات الالتحاق بالدبلومات   ',

        // ─── الحضور والغياب ────────────────────────────
        'view-attendance'                => 'عرض سجلات الحضور',
        'manage-attendance'              => 'إدارة الحضور والغياب',

        // ─── الاختبارات والأسئلة ───────────────────────
        'view-quizzes'                   => 'عرض الاختبارات',
        'create-quizzes'                 => 'إنشاء اختبارات',
        'edit-quizzes'                   => 'تعديل الاختبارات',
        'delete-quizzes'                 => 'حذف الاختبارات',
        'grade-quizzes'                  => 'تصحيح الاختبارات',
        'view-quiz-results'              => 'عرض نتائج الاختبارات',

        // ─── المدفوعات ─────────────────────────────────
        'view-payments'                  => 'عرض المدفوعات',
        'create-payments'                => 'إنشاء سجلات مدفوعات',
        'edit-payments'                  => 'تعديل المدفوعات',
        'record-payments'                => 'تسجيل عمليات الدفع',
        'manage-installments'            => 'إدارة خطط التقسيط',
        'waive-payments'                 => 'إعفاء من المدفوعات',
        'cancel-payments'                => 'إلغاء المدفوعات',

        // ─── التقارير والإحصائيات ──────────────────────
        'view-reports'                   => 'عرض التقارير',
        'export-reports'                 => 'تصدير التقارير',
        'view-analytics'                 => 'عرض الإحصائيات والتحليلات',
        'view-nelc-compliance'           => 'عرض تقارير الامتثال لهيئة تقويم التعليم',
        'view-student-progress'          => 'عرض تقارير تقدم  المتدربون ',
        'view-attendance-reports'        => 'عرض تقارير الحضور',
        'view-grade-reports'             => 'عرض تقارير الدرجات',
        'view-teacher-performance'       => 'عرض تقارير أداء المدربون ',

        // ─── الاستبيانات ───────────────────────────────
        'view-surveys'                   => 'عرض الاستبيانات',
        'create-surveys'                 => 'إنشاء استبيانات',
        'edit-surveys'                   => 'تعديل الاستبيانات',
        'delete-surveys'                 => 'حذف الاستبيانات',

        // ─── تذاكر الدعم الفني ─────────────────────────
        'view-tickets'                   => 'عرض تذاكر الدعم',
        'manage-tickets'                 => 'إدارة تذاكر الدعم',
        'assign-tickets'                 => 'تعيين التذاكر للموظفين',

        // ─── تقييم المدربون  ────────────────────────────
        'view-ratings'                   => 'عرض تقييمات المدربون ',
        'approve-ratings'                => 'قبول تقييمات المدربون ',
        'reject-ratings'                 => 'رفض تقييمات المدربون ',

        // ─── المستخدمون المعلقون ───────────────────────
        'view-pending-users'             => 'عرض طلبات التسجيل المعلقة',
        'approve-pending-users'          => 'قبول طلبات التسجيل',
        'reject-pending-users'           => 'رفض طلبات التسجيل',

        // ─── سجل النشاطات ──────────────────────────────
        'view-activity-logs'             => 'عرض سجل النشاطات',
        'export-activity-logs'           => 'تصدير سجل النشاطات',

        // ─── الأدوار والصلاحيات ────────────────────────
        'manage-roles'                   => 'إدارة الأدوار الوظيفية',
        'manage-permissions'             => 'إدارة الصلاحيات',

        // ─── الإعدادات والنظام ─────────────────────────
        'view-settings'                  => 'عرض إعدادات النظام',
        'edit-settings'                  => 'تعديل إعدادات النظام',
        'manage-system'                  => 'إدارة النظام الكاملة',
        'manage-xapi'                    => 'إدارة بيانات xAPI',

        // ─── المجموعات / الفصول الدراسية ──────────────
        'view-classes'                   => 'عرض المجموعات الدراسية',
        'create-classes'                 => 'إضافة مجموعات دراسية',
        'edit-classes'                   => 'تعديل المجموعات الدراسية',
        'delete-classes'                 => 'حذف المجموعات الدراسية',
        'assign-students-to-class'       => 'إسناد متدربين للمجموعة',

        // ─── الدورات التدريبية ─────────────────────────
        'view-courses'                   => 'عرض الدورات التدريبية',
        'create-courses'                 => 'إضافة دورات تدريبية',
        'edit-courses'                   => 'تعديل الدورات التدريبية',
        'delete-courses'                 => 'حذف الدورات التدريبية',

        // ─── برامج اللغة الإنجليزية ────────────────────
        'view-english'                   => 'عرض برامج اللغة الإنجليزية',
        'create-english'                 => 'إضافة برامج اللغة الإنجليزية',
        'edit-english'                   => 'تعديل برامج اللغة الإنجليزية',
        'delete-english'                 => 'حذف برامج اللغة الإنجليزية',

        // ─── برامج التدريب المهني ──────────────────────
        'view-training-programs'         => 'عرض برامج التدريب المهني',
        'create-training-programs'       => 'إضافة برامج التدريب المهني',
        'edit-training-programs'         => 'تعديل برامج التدريب المهني',
        'delete-training-programs'       => 'حذف برامج التدريب المهني',

        // ─── الجدول الدراسي ────────────────────────────
        'view-schedule'                  => 'عرض الجدول الدراسي',
        'manage-schedule'                => 'إدارة الجدول الدراسي',

        // ─── آراء العملاء / الشهادات ───────────────────
        'view-testimonials'              => 'عرض آراء العملاء',
        'manage-testimonials'            => 'إدارة آراء العملاء',

        // ─── الأخبار والمحتوى ──────────────────────────
        'view-news'                      => 'عرض الأخبار',
        'create-news'                    => 'إضافة أخبار',
        'edit-news'                      => 'تعديل الأخبار',
        'delete-news'                    => 'حذف الأخبار',

        // ─── الأسئلة الشائعة ───────────────────────────
        'view-faqs'                      => 'عرض الأسئلة الشائعة',
        'manage-faqs'                    => 'إدارة الأسئلة الشائعة',

        // ─── طلبات التواصل ─────────────────────────────
        'view-contacts'                  => 'عرض طلبات التواصل',
        'manage-contacts'                => 'إدارة طلبات التواصل',
        'reply-contacts'                 => 'الرد على طلبات التواصل',

        // ─── الشركاء ───────────────────────────────────
        'view-partners'                  => 'عرض الشركاء',
        'manage-partners'                => 'إدارة الشركاء',

        // ─── العروض والخصومات ──────────────────────────
        'view-offers'                    => 'عرض العروض والخصومات',
        'create-offers'                  => 'إضافة عروض وخصومات',
        'edit-offers'                    => 'تعديل العروض والخصومات',
        'delete-offers'                  => 'حذف العروض والخصومات',

        // ─── الصفحات الثابتة ───────────────────────────
        'view-pages'                     => 'عرض الصفحات الثابتة',
        'manage-pages'                   => 'إدارة الصفحات الثابتة',

        // ─── إعدادات الفوتر ────────────────────────────
        'manage-footer'                  => 'إدارة إعدادات الفوتر',

        // ─── واتساب AI ─────────────────────────────────
        'view-whatsapp-chat'             => 'عرض محادثات واتساب',
        'manage-whatsapp-chat'           => 'إدارة محادثات واتساب',

        // ─── سجل الأخطاء (Logs) ────────────────────────
        'view-logs'                      => 'عرض سجل أخطاء النظام',
        'manage-logs'                    => 'إدارة سجل الأخطاء',
    ];

    /**
     * Permissions assigned per role.
     * Use 'syncPermissions' so re-running always stays consistent.
     */
    public static array $rolePermissions = [

        'super-admin' => '*', // gets ALL permissions

        'admin' => [
            'view-dashboard',
            // Users & People
            'view-users', 'create-users', 'edit-users', 'toggle-user-status',
            'view-teachers', 'create-teachers', 'edit-teachers', 'delete-teachers',
            'view-students', 'create-students', 'edit-students', 'delete-students',
            'assign-program-to-student', 'remove-program-from-student', 'toggle-student-status',
            // Programs / Terms / Subjects
            'view-programs', 'create-programs', 'edit-programs', 'delete-programs',
            'view-terms', 'create-terms', 'edit-terms', 'delete-terms',
            'view-subjects', 'create-subjects', 'edit-subjects', 'delete-subjects',
            // Sessions
            'view-sessions', 'create-sessions', 'edit-sessions', 'delete-sessions', 'manage-zoom',
            // Recordings
            'view-recordings', 'manage-recordings', 'sync-recordings', 'delete-recordings',
            // Enrollments
            'view-enrollments', 'manage-enrollments',
            'manage-program-enrollments', 'approve-program-enrollments', 'reject-program-enrollments',
            // Attendance
            'view-attendance', 'manage-attendance',
            // Quizzes
            'view-quizzes', 'create-quizzes', 'edit-quizzes', 'delete-quizzes',
            'grade-quizzes', 'view-quiz-results',
            // Payments
            'view-payments', 'create-payments', 'edit-payments', 'record-payments',
            'manage-installments', 'waive-payments', 'cancel-payments',
            // Reports
            'view-reports', 'export-reports', 'view-analytics',
            'view-nelc-compliance', 'view-student-progress',
            'view-attendance-reports', 'view-grade-reports', 'view-teacher-performance',
            // Surveys
            'view-surveys', 'create-surveys', 'edit-surveys', 'delete-surveys',
            // Tickets
            'view-tickets', 'manage-tickets', 'assign-tickets',
            // Ratings
            'view-ratings', 'approve-ratings', 'reject-ratings',
            // Pending users
            'view-pending-users', 'approve-pending-users', 'reject-pending-users',
            // Logs
            'view-activity-logs', 'export-activity-logs',
            // Settings (no manage-system)
            'view-settings', 'edit-settings',
            // xAPI
            'manage-xapi',
            // Classes
            'view-classes', 'create-classes', 'edit-classes', 'delete-classes', 'assign-students-to-class',
            // Courses / English / Training
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses',
            'view-english', 'create-english', 'edit-english', 'delete-english',
            'view-training-programs', 'create-training-programs', 'edit-training-programs', 'delete-training-programs',
            // Schedule
            'view-schedule', 'manage-schedule',
            // Content
            'view-testimonials', 'manage-testimonials',
            'view-news', 'create-news', 'edit-news', 'delete-news',
            'view-faqs', 'manage-faqs',
            'view-contacts', 'manage-contacts', 'reply-contacts',
            'view-partners', 'manage-partners',
            'view-offers', 'create-offers', 'edit-offers', 'delete-offers',
            'view-pages', 'manage-pages',
            'manage-footer',
            // WhatsApp / Logs
            'view-whatsapp-chat', 'manage-whatsapp-chat',
            'view-logs', 'manage-logs',
        ],

        'teacher' => [
            'view-dashboard',
            'view-sessions', 'create-sessions', 'edit-sessions', 'delete-sessions',
            'view-subjects',
            'view-enrollments',
            'view-attendance', 'manage-attendance',
            'view-quizzes', 'create-quizzes', 'edit-quizzes', 'delete-quizzes',
            'grade-quizzes', 'view-quiz-results',
            'view-reports',
            'view-surveys',
            'view-tickets',
            'view-ratings',
        ],

        'student' => [
            'view-dashboard',
            'view-sessions',
            'view-subjects',
            'view-attendance',
            'view-quizzes',
            'view-surveys',
            'view-tickets',
        ],

        'content-manager' => [
            'view-dashboard',
            'view-sessions', 'create-sessions', 'edit-sessions',
            'view-subjects', 'create-subjects', 'edit-subjects',
            'view-programs', 'edit-programs',
            'view-terms',
            'view-recordings', 'manage-recordings', 'sync-recordings',
            'view-quizzes', 'create-quizzes', 'edit-quizzes',
            'view-news', 'create-news', 'edit-news', 'delete-news',
            'view-faqs', 'manage-faqs',
            'view-testimonials', 'manage-testimonials',
            'view-pages', 'manage-pages',
            'manage-footer',
            'view-courses', 'create-courses', 'edit-courses',
            'view-english', 'create-english', 'edit-english',
            'view-training-programs', 'create-training-programs', 'edit-training-programs',
        ],

        'support-agent' => [
            'view-dashboard',
            'view-users',
            'view-students',
            'view-tickets', 'manage-tickets', 'assign-tickets',
            'view-surveys',
            'view-pending-users', 'approve-pending-users', 'reject-pending-users',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Create or update all permissions (safe to re-run) ────────
        $created = 0;
        $updated = 0;

        foreach (self::$permissions as $name => $arabicName) {
            $exists = Permission::where('name', $name)->where('guard_name', 'web')->exists();

            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );

            $exists ? $updated++ : $created++;
        }

        $this->command->info("✅ الصلاحيات: {$created} جديدة، {$updated} موجودة (المجموع: " . count(self::$permissions) . ')');

        // ── 2. Remove permissions that no longer exist in the list ───────
        $defined = array_keys(self::$permissions);
        $removed = Permission::whereNotIn('name', $defined)->where('guard_name', 'web')->delete();
        if ($removed > 0) {
            $this->command->warn("🗑️  تم حذف {$removed} صلاحية قديمة غير موجودة في القائمة");
        }

        // ── 3. Create roles and sync permissions ─────────────────────────
        foreach (self::$rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($perms === '*') {
                $role->syncPermissions(Permission::all());
                $this->command->info("✅ الدور [{$roleName}]: جميع الصلاحيات ({$role->permissions()->count()})");
            } else {
                // Only sync permissions that actually exist
                $validPerms = array_filter($perms, fn ($p) => isset(self::$permissions[$p]));
                $role->syncPermissions($validPerms);
                $this->command->info("✅ الدور [{$roleName}]: {$role->permissions()->count()} صلاحية");
            }
        }

        // Reset cache again after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🎉 تم الانتهاء من إعداد جميع الصلاحيات والأدوار');
    }
}
