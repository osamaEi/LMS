<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Evaluation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting complete system seeding...');

        // 1. إنشاء مدرسين
        $this->command->info('Creating teachers...');
        $teachers = $this->createTeachers();

        // 2. إنشاء طلاب
        $this->command->info('Creating students...');
        $students = $this->createStudents();

        // 3. الحصول على المسارات الموجودة
        $tracks = Track::with('terms')->get();

        foreach ($tracks->take(1) as $track) { // فقط أول مسار للاختبار
            $this->command->info("Processing track: {$track->name}");

            // 4. إنشاء مواد لكل ربع في المسار
            foreach ($track->terms->take(2) as $term) { // فقط الربع 1 و 2
                $this->command->info("  Creating subjects for term {$term->term_number}...");
                $subjects = $this->createSubjectsForTerm($term, $teachers);

                // 5. إنشاء وحدات وجلسات لكل مادة
                foreach ($subjects as $subject) {
                    $this->command->info("    Creating units for subject: {$subject->name}");
                    $units = $this->createUnitsForSubject($subject);

                    // 6. إنشاء جلسات لكل وحدة
                    foreach ($units as $unit) {
                        $this->createSessionsForUnit($unit);
                    }
                }

                // 7. تسجيل الطلاب في المواد
                if ($term->term_number == 1) {
                    $this->command->info("    Enrolling students in subjects...");
                    $this->enrollStudentsInSubjects($students, $subjects);
                }
            }
        }

        // 8. إنشاء سجلات حضور للطلاب
        $this->command->info('Creating attendance records...');
        $this->createAttendanceRecords($students);

        // 9. إنشاء تقييمات
        $this->command->info('Creating evaluations...');
        $this->createEvaluations($students);

        $this->command->info('Complete system seeding finished!');
    }

    protected function createTeachers(): array
    {
        $teachers = [
            [
                'name' => 'د. محمد أحمد',
                'email' => 'teacher1@lms.com',
                'specialization' => 'هندسة البرمجيات',
                'bio' => 'خبرة 15 سنة في تطوير البرمجيات والتدريس الأكاديمي',
                'profile_photo' => 'https://ui-avatars.com/api/?name=محمد+أحمد&background=4F46E5&color=fff',
            ],
            [
                'name' => 'د. فاطمة علي',
                'email' => 'teacher2@lms.com',
                'specialization' => 'أمن المعلومات',
                'bio' => 'متخصصة في الأمن السيبراني وحماية البيانات',
                'profile_photo' => 'https://ui-avatars.com/api/?name=فاطمة+علي&background=10B981&color=fff',
            ],
            [
                'name' => 'د. خالد سعيد',
                'email' => 'teacher3@lms.com',
                'specialization' => 'قواعد البيانات',
                'bio' => 'خبير في تصميم وإدارة قواعد البيانات الكبيرة',
                'profile_photo' => 'https://ui-avatars.com/api/?name=خالد+سعيد&background=F59E0B&color=fff',
            ],
        ];

        $created = [];
        foreach ($teachers as $teacher) {
            $created[] = User::firstOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'teacher',
                    'specialization' => $teacher['specialization'],
                    'bio' => $teacher['bio'],
                    'profile_photo' => $teacher['profile_photo'],
                    'status' => 'active',
                ]
            );
        }

        return $created;
    }

    protected function createStudents(): array
    {
        $track = Track::first();

        if (!$track) {
            $this->command->error('No tracks found. Run TrackSeeder first!');
            return [];
        }

        $students = [
            ['name' => 'أحمد محمود', 'email' => 'student1@lms.com'],
            ['name' => 'سارة عبدالله', 'email' => 'student2@lms.com'],
            ['name' => 'محمد حسن', 'email' => 'student3@lms.com'],
        ];

        $created = [];
        foreach ($students as $student) {
            $created[] = User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'status' => 'active',
                    'program_id' => $track->program_id,
                    'track_id' => $track->id,
                    'current_term_number' => 1,
                    'profile_photo' => 'https://ui-avatars.com/api/?name=' . urlencode($student['name']) . '&background=6366F1&color=fff',
                ]
            );
        }

        return $created;
    }

    protected function createSubjectsForTerm(Term $term, array $teachers): array
    {
        $subjectsData = [
            [
                'name' => 'مقدمة في البرمجة',
                'code' => 'CS101',
                'description' => 'تعلم أساسيات البرمجة باستخدام لغة Python',
                'banner_photo' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800',
                'total_hours' => 40,
                'credits' => 3,
            ],
            [
                'name' => 'قواعد البيانات',
                'code' => 'CS102',
                'description' => 'تصميم وإدارة قواعد البيانات العلائقية',
                'banner_photo' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800',
                'total_hours' => 35,
                'credits' => 3,
            ],
            [
                'name' => 'تطوير الويب',
                'code' => 'CS103',
                'description' => 'بناء تطبيقات الويب الحديثة',
                'banner_photo' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
                'total_hours' => 45,
                'credits' => 4,
            ],
        ];

        $subjects = [];
        foreach ($subjectsData as $index => $data) {
            $teacher = $teachers[$index % count($teachers)];

            $subjects[] = Subject::firstOrCreate(
                [
                    'term_id' => $term->id,
                    'code' => $data['code'] . '-T' . $term->term_number,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'banner_photo' => $data['banner_photo'],
                    'total_hours' => $data['total_hours'],
                    'credits' => $data['credits'],
                    'max_students' => 30,
                    'status' => 'active',
                ]
            );
        }

        return $subjects;
    }

    protected function createUnitsForSubject(Subject $subject): array
    {
        $unitsData = [
            [
                'title' => 'الوحدة الأولى: المقدمة',
                'description' => 'مقدمة عن المادة والأهداف التعليمية',
                'duration_hours' => 8,
                'learning_objectives' => 'فهم المفاهيم الأساسية والأهداف',
            ],
            [
                'title' => 'الوحدة الثانية: المفاهيم المتقدمة',
                'description' => 'التعمق في المفاهيم المتقدمة',
                'duration_hours' => 12,
                'learning_objectives' => 'إتقان المفاهيم المتقدمة والتطبيق العملي',
            ],
            [
                'title' => 'الوحدة الثالثة: التطبيقات العملية',
                'description' => 'تطبيق ما تم تعلمه في مشاريع عملية',
                'duration_hours' => 10,
                'learning_objectives' => 'القدرة على بناء مشاريع كاملة',
            ],
        ];

        $units = [];
        foreach ($unitsData as $index => $data) {
            $units[] = Unit::firstOrCreate(
                [
                    'subject_id' => $subject->id,
                    'unit_number' => $index + 1,
                ],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'duration_hours' => $data['duration_hours'],
                    'learning_objectives' => $data['learning_objectives'],
                    'status' => 'published',
                    'order' => $index + 1,
                ]
            );
        }

        return $units;
    }

    protected function createSessionsForUnit(Unit $unit): void
    {
        $sessionsData = [
            [
                'title' => 'الجلسة الأولى: المقدمة',
                'description' => 'شرح مفصل للمفاهيم الأساسية',
                'duration' => 90,
            ],
            [
                'title' => 'الجلسة الثانية: التطبيق العملي',
                'description' => 'تطبيقات عملية على المفاهيم',
                'duration' => 120,
            ],
        ];

        foreach ($sessionsData as $index => $sessionData) {
            $session = Session::firstOrCreate(
                [
                    'unit_id' => $unit->id,
                    'session_number' => $index + 1,
                ],
                [
                    'subject_id' => $unit->subject_id,
                    'title' => $sessionData['title'],
                    'description' => $sessionData['description'],
                    'type' => 'mixed', // الجلسة تحتوي على أنواع مختلفة من الملفات
                    'scheduled_at' => now()->addDays($index + 1),
                    'duration_minutes' => $sessionData['duration'],
                    'status' => 'completed',
                    'is_mandatory' => true,
                    'video_platform' => 'local',
                ]
            );

            // إنشاء الملفات للجلسة (2 فيديو، 1 PDF، 2 Zoom)
            $this->createFilesForSession($session, $index);
        }
    }

    protected function createFilesForSession(Session $session, int $sessionIndex): void
    {
        // فيديو 1
        SessionFile::firstOrCreate(
            [
                'session_id' => $session->id,
                'type' => 'video',
                'order' => 1,
            ],
            [
                'title' => 'الفيديو التعليمي - الجزء الأول',
                'description' => 'شرح المفاهيم النظرية',
                'video_path' => "videos/session_{$session->id}/part1.mp4",
                'video_platform' => 'local',
                'video_duration' => 45,
                'video_size' => 104857600, // 100 MB
                'is_mandatory' => true,
            ]
        );

        // فيديو 2
        SessionFile::firstOrCreate(
            [
                'session_id' => $session->id,
                'type' => 'video',
                'order' => 2,
            ],
            [
                'title' => 'الفيديو التعليمي - الجزء الثاني',
                'description' => 'التطبيق العملي والأمثلة',
                'video_url' => 'https://www.youtube.com/watch?v=example' . $sessionIndex,
                'video_platform' => 'youtube',
                'video_duration' => 60,
                'is_mandatory' => true,
            ]
        );

        // PDF
        SessionFile::firstOrCreate(
            [
                'session_id' => $session->id,
                'type' => 'pdf',
                'order' => 3,
            ],
            [
                'title' => 'ملف الشرح والملاحظات',
                'description' => 'ملف PDF يحتوي على ملاحظات الدرس والأمثلة',
                'file_path' => "pdfs/session_{$session->id}/notes.pdf",
                'file_size' => 5242880, // 5 MB
                'is_mandatory' => false,
            ]
        );

        // Zoom 1
        SessionFile::firstOrCreate(
            [
                'session_id' => $session->id,
                'type' => 'zoom',
                'order' => 4,
            ],
            [
                'title' => 'جلسة مباشرة - النقاش والأسئلة',
                'description' => 'جلسة مباشرة للنقاش والإجابة على الأسئلة',
                'zoom_meeting_id' => '12345678' . $sessionIndex . '1',
                'zoom_join_url' => 'https://zoom.us/j/12345678' . $sessionIndex . '1',
                'zoom_password' => 'pass123',
                'zoom_scheduled_at' => now()->addDays($sessionIndex + 1)->setTime(18, 0),
                'zoom_duration' => 60,
                'is_mandatory' => true,
            ]
        );

        // Zoom 2
        SessionFile::firstOrCreate(
            [
                'session_id' => $session->id,
                'type' => 'zoom',
                'order' => 5,
            ],
            [
                'title' => 'جلسة مباشرة - ورشة عمل',
                'description' => 'ورشة عمل تطبيقية للمفاهيم',
                'zoom_meeting_id' => '12345678' . $sessionIndex . '2',
                'zoom_join_url' => 'https://zoom.us/j/12345678' . $sessionIndex . '2',
                'zoom_password' => 'workshop123',
                'zoom_scheduled_at' => now()->addDays($sessionIndex + 2)->setTime(19, 0),
                'zoom_duration' => 90,
                'is_mandatory' => false,
            ]
        );
    }

    protected function enrollStudentsInSubjects(array $students, array $subjects): void
    {
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'enrolled_at' => now(),
                        'status' => 'active',
                    ]
                );
            }
        }
    }

    protected function createAttendanceRecords(array $students): void
    {
        $enrollments = Enrollment::with('subject.sessions')->get();

        foreach ($enrollments as $enrollment) {
            $sessions = $enrollment->subject->sessions;

            foreach ($sessions as $session) {
                $attended = rand(0, 100) > 20; // 80% احتمال الحضور

                Attendance::firstOrCreate(
                    [
                        'student_id' => $enrollment->student_id,
                        'session_id' => $session->id,
                    ],
                    [
                        'attended' => $attended,
                        'watch_percentage' => $attended ? rand(70, 100) : 0,
                        'video_completed' => $attended && rand(0, 100) > 30,
                        'joined_at' => $attended ? now()->subDays(rand(1, 7)) : null,
                        'duration_minutes' => $attended ? rand(30, $session->duration_minutes) : 0,
                    ]
                );
            }
        }
    }

    protected function createEvaluations(array $students): void
    {
        $enrollments = Enrollment::with('subject')->get();

        foreach ($enrollments as $enrollment) {
            // تقييم 1: واجب
            $earned1 = rand(70, 95);
            Evaluation::firstOrCreate(
                [
                    'subject_id' => $enrollment->subject_id,
                    'student_id' => $enrollment->student_id,
                    'type' => 'assignment',
                ],
                [
                    'title' => 'الواجب الأول',
                    'total_score' => 100,
                    'earned_score' => $earned1,
                    'percentage' => $earned1,
                    'weight' => 20,
                    'status' => 'graded',
                    'graded_at' => now(),
                ]
            );

            // تقييم 2: اختبار منتصف الفصل
            $earned2 = rand(65, 90);
            Evaluation::firstOrCreate(
                [
                    'subject_id' => $enrollment->subject_id,
                    'student_id' => $enrollment->student_id,
                    'type' => 'midterm_exam',
                ],
                [
                    'title' => 'اختبار منتصف الفصل',
                    'total_score' => 100,
                    'earned_score' => $earned2,
                    'percentage' => $earned2,
                    'weight' => 30,
                    'status' => 'graded',
                    'graded_at' => now(),
                ]
            );
        }
    }
}
