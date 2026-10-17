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
        $service->shouldReceive('savedFinalGrades')->with($student)->andReturn(collect());
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
        $service->shouldReceive('savedFinalGrades')->with($student)->andReturn(collect());
        $service->shouldReceive('sentGradeReports')->with($student)->andReturn(collect());

        $response = (new GradesController)->index($request, $service);

        $this->assertSame(['result' => []], $response->getData(true));
    }

    public function test_saved_grades_appear_without_sending_and_override_old_report_totals(): void
    {
        $student = new User(['role' => 'student']);
        $request = Request::create('/api/v1/student/grades');
        $request->setUserResolver(fn () => $student);
        $service = $this->mock(StudentGradesService::class);
        $service->shouldReceive('sentGradeReports')->with($student)->andReturn(collect([
            (object) ['rows' => collect([
                ['subject_id' => 7, 'name' => 'Subject', 'attendance' => 8, 'total' => 50],
            ])],
        ]));
        $service->shouldReceive('savedFinalGrades')->with($student)->andReturn(collect([
            ['subject_id' => 7, 'name' => 'Subject', 'total' => 90],
            ['subject_id' => 8, 'name' => 'Unsent subject', 'total' => 0],
        ]));

        $data = (new GradesController)->index($request, $service)->getData(true);

        $this->assertSame(['result'], array_keys($data));
        $this->assertCount(2, $data['result']);
        $this->assertSame(90, $data['result'][0]['total']['score']);
        $this->assertSame('A', $data['result'][0]['grade_letter']);
        $this->assertSame(8, $data['result'][0]['attendance']['score']);
        $this->assertSame('8', $data['result'][1]['subject_id']);
        $this->assertSame(0, $data['result'][1]['total']['score']);
        $this->assertSame('F', $data['result'][1]['grade_letter']);
        $this->assertNull($data['result'][1]['final']['score']);
    }
}
