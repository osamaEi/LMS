<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Program;
use App\Models\Term;
use App\Models\Subject;
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
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'national_id' => '1234567890',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Create Teachers
        $teacher1 = User::firstOrCreate(
            ['email' => 'teacher@lms.com'],
            [
                'name' => 'محمد السبيعي',
                'password' => Hash::make('password'),
                'national_id' => '0987654321',
                'role' => 'teacher',
                'status' => 'active',
            ]
        );

        $teacher2 = User::firstOrCreate(
            ['email' => 'teacher2@lms.com'],
            [
                'name' => 'فاطمة أحمد',
                'password' => Hash::make('password'),
                'national_id' => '1231231234',
                'role' => 'teacher',
                'status' => 'active',
            ]
        );

        // Create Students
        $student1 = User::firstOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'أحمد محمود',
                'password' => Hash::make('password'),
                'national_id' => '1122334455',
                'role' => 'student',
                'status' => 'active',
            ]
        );

        $student2 = User::firstOrCreate(
            ['email' => 'student2@lms.com'],
            [
                'name' => 'سارة خالد',
                'password' => Hash::make('password'),
                'national_id' => '5544332211',
                'role' => 'student',
                'status' => 'active',
            ]
        );

        $student3 = User::firstOrCreate(
            ['email' => 'student3@lms.com'],
            [
                'name' => 'علي حسن',
                'password' => Hash::make('password'),
                'national_id' => '9988776655',
                'role' => 'student',
                'status' => 'active',
            ]
        );

        // Create Program
        $program = Program::firstOrCreate(
            ['code' => 'PROG-001'],
            [
                'name_ar' => 'دبلوم البرمجة وتطوير الويب',
                'name_en' => 'Web Development Diploma',
                'duration_months' => 12,
                'price' => 5000,
                'status' => 'active',
                'description_ar' => 'برنامج شامل لتعلم البرمجة وتطوير الويب',
                'description_en' => 'A comprehensive program to learn programming and web development',
            ]
        );

        // Create Terms
        $term1 = Term::firstOrCreate(
            ['program_id' => $program->id, 'term_number' => 1],
            [
                'name_ar' => 'الفصل الأول',
                'name_en' => 'First Semester',
                'start_date' => now(),
                'end_date' => now()->addMonths(4),
                'status' => 'active',
            ]
        );

        $term2 = Term::firstOrCreate(
            ['program_id' => $program->id, 'term_number' => 2],
            [
                'name_ar' => 'الفصل الثاني',
                'name_en' => 'Second Semester',
                'start_date' => now()->addMonths(4),
                'end_date' => now()->addMonths(8),
                'status' => 'upcoming',
            ]
        );

        // Create Subjects
        $subject1 = Subject::firstOrCreate(
            ['code' => 'WEB101'],
            [
                'name_ar' => 'أساسيات تطوير الويب',
                'name_en' => 'Web Development Fundamentals',
                'term_id' => $term1->id,
                'teacher_id' => $teacher1->id,
                'status' => 'active',
                'description_ar' => 'مقدمة في HTML, CSS و JavaScript',
                'description_en' => 'Introduction to HTML, CSS and JavaScript',
            ]
        );

        $subject2 = Subject::firstOrCreate(
            ['code' => 'PHP101'],
            [
                'name_ar' => 'برمجة PHP',
                'name_en' => 'PHP Programming',
                'term_id' => $term1->id,
                'teacher_id' => $teacher1->id,
                'status' => 'active',
                'description_ar' => 'تعلم لغة PHP من الصفر',
                'description_en' => 'Learn PHP from scratch',
            ]
        );

        $subject3 = Subject::firstOrCreate(
            ['code' => 'DB101'],
            [
                'name_ar' => 'قواعد البيانات',
                'name_en' => 'Database Management',
                'term_id' => $term1->id,
                'teacher_id' => $teacher2->id,
                'status' => 'active',
                'description_ar' => 'تصميم وإدارة قواعد البيانات',
                'description_en' => 'Database design and management',
            ]
        );

        // Create Enrollments
        Enrollment::firstOrCreate(
            ['student_id' => $student1->id, 'subject_id' => $subject1->id],
            [
                'status' => 'active',
                'enrolled_at' => now()->subDays(10),
                'progress' => 35,
            ]
        );

        Enrollment::firstOrCreate(
            ['student_id' => $student1->id, 'subject_id' => $subject2->id],
            [
                'status' => 'active',
                'enrolled_at' => now()->subDays(5),
                'progress' => 20,
            ]
        );

        Enrollment::firstOrCreate(
            ['student_id' => $student2->id, 'subject_id' => $subject1->id],
            [
                'status' => 'active',
                'enrolled_at' => now()->subDays(15),
                'progress' => 50,
            ]
        );

        Enrollment::firstOrCreate(
            ['student_id' => $student2->id, 'subject_id' => $subject3->id],
            [
                'status' => 'active',
                'enrolled_at' => now()->subDays(7),
                'progress' => 40,
            ]
        );

        Enrollment::firstOrCreate(
            ['student_id' => $student3->id, 'subject_id' => $subject2->id],
            [
                'status' => 'active',
                'enrolled_at' => now()->subDays(3),
                'progress' => 15,
            ]
        );

        $this->command->info('✅ Demo data created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@lms.com / password');
        $this->command->info('Teacher: teacher@lms.com / password');
        $this->command->info('Student: student@lms.com / password');
    }
}
