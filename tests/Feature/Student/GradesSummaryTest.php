<?php

namespace Tests\Feature\Student;

use App\Models\Subject;
use App\Models\User;
use App\Services\StudentGradesService;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GradesSummaryTest extends TestCase
{
    public function test_summary_returns_only_subject_grade_and_evaluation(): void
    {
        $student = new User(['role' => 'student']);
        Sanctum::actingAs($student);
        $this->mock(StudentGradesService::class)->shouldReceive('overview')
            ->once()->with($student)->andReturn(['subjectGrades' => [
                105 => ['subject' => new Subject(['name_ar' => 'الرياضيات']),
                    'percentage' => 73, 'grade_label' => 'جيد'],
                106 => ['subject' => new Subject(['name_ar' => 'العلوم']),
                    'percentage' => 0, 'grade_label' => 'ضعيف'],
            ]]);

        $this->getJson('/api/v1/student/grades/summary')->assertOk()->assertExactJson([
            'result' => [
                ['subject_name' => 'الرياضيات', 'grade' => 73, 'evaluation' => 'جيد'],
                ['subject_name' => 'العلوم', 'grade' => 0, 'evaluation' => 'ضعيف'],
            ],
        ]);
    }

    public function test_summary_is_empty_when_no_subject_grades_exist(): void
    {
        $student = new User(['role' => 'student']);
        Sanctum::actingAs($student);
        $this->mock(StudentGradesService::class)->shouldReceive('overview')
            ->once()->with($student)->andReturn(['subjectGrades' => []]);

        $this->getJson('/api/v1/student/grades/summary')
            ->assertOk()->assertExactJson(['result' => []]);
    }

    public function test_summary_requires_authentication(): void
    {
        $this->getJson('/api/v1/student/grades/summary')->assertUnauthorized();
    }

    public function test_summary_rejects_non_students(): void
    {
        Sanctum::actingAs(new User(['role' => 'teacher']));
        $this->getJson('/api/v1/student/grades/summary')->assertForbidden();
    }
}
