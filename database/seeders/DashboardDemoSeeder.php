<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DashboardDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '1234567890',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Teachers
        $teacher1 = User::create([
            'name' => 'محمد السبيعي',
            'email' => 'teacher@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '0987654321',
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $teacher2 = User::create([
            'name' => 'فاطمة أحمد',
            'email' => 'teacher2@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '1231231234',
            'role' => 'teacher',
            'status' => 'active',
        ]);

        // Create Students
        $student1 = User::create([
            'name' => 'أحمد محمود',
            'email' => 'student@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '1122334455',
            'role' => 'student',
            'status' => 'active',
        ]);

        $student2 = User::create([
            'name' => 'سارة خالد',
            'email' => 'student2@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '5544332211',
            'role' => 'student',
            'status' => 'active',
        ]);

        $student3 = User::create([
            'name' => 'علي حسن',
            'email' => 'student3@lms.com',
            'password' => Hash::make('password'),
            'national_id' => '9988776655',
            'role' => 'student',
            'status' => 'active',
        ]);

        // Create Courses
        $course1 = Course::create([
            'title' => 'أساسيات قواعد البيانات',
            'description' => 'دورة عملية لتعلم أساسيات قواعد البيانات من البداية وكيفية إدارتها بشكل احترافي باستخدام SQL',
            'teacher_id' => $teacher1->id,
            'status' => 'active',
            'max_students' => 30,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
        ]);

        $course2 = Course::create([
            'title' => 'تطوير تطبيقات الويب',
            'description' => 'تعلم بناء تطبيقات الويب الحديثة باستخدام Laravel و Vue.js مع أفضل الممارسات',
            'teacher_id' => $teacher1->id,
            'status' => 'active',
            'max_students' => 25,
            'start_date' => now(),
            'end_date' => now()->addMonths(4),
        ]);

        $course3 = Course::create([
            'title' => 'مهارات التواصل الفعّال',
            'description' => 'دورة شاملة لتطوير مهارات التواصل في بيئة العمل والحياة اليومية',
            'teacher_id' => $teacher2->id,
            'status' => 'active',
            'max_students' => 40,
            'start_date' => now()->subWeek(),
            'end_date' => now()->addMonths(2),
        ]);

        $course4 = Course::create([
            'title' => 'البرمجة الموجهة بالكائنات',
            'description' => 'فهم المفاهيم الأساسية للبرمجة الموجهة بالكائنات وتطبيقها عملياً',
            'teacher_id' => $teacher2->id,
            'status' => 'active',
            'max_students' => 30,
            'start_date' => now()->addWeek(),
            'end_date' => now()->addMonths(3),
        ]);

        // Enroll students in courses
        Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(10),
            'progress' => 35,
        ]);

        Enrollment::create([
            'student_id' => $student1->id,
            'course_id' => $course2->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(5),
            'progress' => 20,
        ]);

        Enrollment::create([
            'student_id' => $student2->id,
            'course_id' => $course1->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(15),
            'progress' => 50,
        ]);

        Enrollment::create([
            'student_id' => $student2->id,
            'course_id' => $course3->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(7),
            'progress' => 40,
        ]);

        Enrollment::create([
            'student_id' => $student3->id,
            'course_id' => $course2->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(3),
            'progress' => 15,
        ]);

        $this->command->info('✅ Demo data created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@lms.com / password');
        $this->command->info('Teacher: teacher@lms.com / password');
        $this->command->info('Student: student@lms.com / password');
    }
}
