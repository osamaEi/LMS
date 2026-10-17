<?php

namespace Tests\Feature\Student;

use App\Http\Controllers\Api\V1\Student\GradesController;
use App\Models\User;
use App\Services\StudentGradesService;
use Illuminate\Http\Request;
use Tests\TestCase;

class GradesResponseTest extends TestCase
{
    public function test_only_latest_sent_result_per_subject_is_returned(): void
    {
        $student = new User(['role' => 'student']);
        $request = Request::create('/api/v1/student/grades');
        $request->setUserResolver(fn () => $student);
        $row = ['subject_id' => 7, 'name' => 'Subject', 'attendance' => 0,
            'participation' => 0, 'midterm' => 0, 'final' => 0, 'total' => 0];
        $service = $this->mock(StudentGradesService::class);
        $service->shouldReceive('sentGradeReports')->once()->with($student)->andReturn(collect([
            (object) ['rows' => collect([$row])],
            (object) ['rows' => collect([array_replace($row, ['total' => 90])])],
        ]));

        $response = (new GradesController)->index($request, $service);

        $this->assertSame(['result' => [[
            'subject_id' => '7', 'subject_name' => 'Subject',
            'attendance' => ['score' => 0, 'max' => 10],
            'participation' => ['score' => 0, 'max' => 30],
            'midterm' => ['score' => 0, 'max' => 20],
            'final' => ['score' => 0, 'max' => 40],
            'total' => ['score' => 0, 'max' => 100],
            'grade_letter' => 'F', 'grade_label' => 'راسب',
        ]]], $response->getData(true));
    }

    public function test_no_sent_grades_returns_an_empty_result(): void
    {
        $student = new User(['role' => 'student']);
        $request = Request::create('/api/v1/student/grades');
        $request->setUserResolver(fn () => $student);
        $service = $this->mock(StudentGradesService::class);
        $service->shouldReceive('sentGradeReports')->with($student)->andReturn(collect());

        $response = (new GradesController)->index($request, $service);

        $this->assertSame(['result' => []], $response->getData(true));
    }
}
