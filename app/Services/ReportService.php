<?php

namespace App\Services;

use App\Models\{User, Program, Subject, Attendance, Evaluation, ActivityLog, XapiStatement};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Student Progress Report
     */
    public function studentProgressReport(int $studentId, ?int $subjectId = null): array
    {
        $student = User::with(['enrollments.subject'])->findOrFail($studentId);

        $query = $student->enrollments();
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $enrollments = $query->with(['subject', 'evaluations'])->get();

        $progress = $enrollments->map(function ($enrollment) {
            $evaluations = $enrollment->evaluations;
            $totalScore = $evaluations->where('score', '!=', null)->avg('score') ?? 0;
            $completedEvaluations = $evaluations->whereNotNull('score')->count();
            $totalEvaluations = $evaluations->count();

            return [
                'subject' => $enrollment->subject->name,
                'total_evaluations' => $totalEvaluations,
                'completed_evaluations' => $completedEvaluations,
                'average_score' => round($totalScore, 2),
                'progress_percentage' => $totalEvaluations > 0 ? round(($completedEvaluations / $totalEvaluations) * 100, 2) : 0,
                'status' => $enrollment->status,
            ];
        });

        return [
            'student' => $student,
            'progress' => $progress,
            'overall_average' => round($progress->avg('average_score'), 2),
            'overall_progress' => round($progress->avg('progress_percentage'), 2),
        ];
    }

    /**
     * Attendance Report
     */
    public function attendanceReport(array $filters = []): array
    {
        $query = Attendance::with(['student', 'session.subject']);

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['subject_id'])) {
            $query->whereHas('session', fn($q) => $q->where('subject_id', $filters['subject_id']));
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $attendances = $query->get();

        $totalSessions = $attendances->count();
        $attendedSessions = $attendances->where('attended', true)->count();
        $attendanceRate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0;

        return [
            'attendances' => $attendances,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedSessions,
            'missed_sessions' => $totalSessions - $attendedSessions,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Grades Report
     */
    public function gradesReport(int $subjectId): array
    {
        $subject = Subject::with(['evaluations.student'])->findOrFail($subjectId);

        $evaluations = $subject->evaluations;

        $gradeDistribution = [
            'A+' => $evaluations->where('letter_grade', 'A+')->count(),
            'A' => $evaluations->where('letter_grade', 'A')->count(),
            'B+' => $evaluations->where('letter_grade', 'B+')->count(),
            'B' => $evaluations->where('letter_grade', 'B')->count(),
            'C+' => $evaluations->where('letter_grade', 'C+')->count(),
            'C' => $evaluations->where('letter_grade', 'C')->count(),
            'D' => $evaluations->where('letter_grade', 'D')->count(),
            'F' => $evaluations->where('letter_grade', 'F')->count(),
        ];

        return [
            'subject' => $subject,
            'evaluations' => $evaluations,
            'grade_distribution' => $gradeDistribution,
            'average_score' => round($evaluations->avg('score'), 2),
            'highest_score' => $evaluations->max('score'),
            'lowest_score' => $evaluations->min('score'),
        ];
    }

    /**
     * Teacher Performance Report
     */
    public function teacherPerformanceReport(int $teacherId): array
    {
        $teacher = User::with(['subjects'])->findOrFail($teacherId);

        $subjects = $teacher->subjects()->with(['sessions', 'enrollments'])->get();

        $totalSessions = $subjects->sum(fn($s) => $s->sessions->count());
        $totalStudents = $subjects->sum(fn($s) => $s->enrollments->count());

        // Average rating
        $ratings = \App\Models\TeacherRating::where('teacher_id', $teacherId)
            ->where('is_approved', true)
            ->get();

        return [
            'teacher' => $teacher,
            'total_subjects' => $subjects->count(),
            'total_sessions' => $totalSessions,
            'total_students' => $totalStudents,
            'average_rating' => round($ratings->avg('overall_rating'), 2),
            'total_ratings' => $ratings->count(),
        ];
    }

    /**
     * NELC Compliance Report
     */
    public function nelcComplianceReport(): array
    {
        // Satisfaction Rate (NELC 1.2.11)
        $avgSatisfaction = \App\Models\SurveyResponse::whereNotNull('rating')->avg('rating');
        $satisfactionRate = $avgSatisfaction ? round(($avgSatisfaction / 5) * 100, 1) : 0;

        // Support Response Time (NELC 1.3.3)
        $tickets = \App\Models\Ticket::whereNotNull('first_response_at')->get();
        $avgResponseMinutes = 0;
        if ($tickets->count() > 0) {
            $totalMinutes = $tickets->sum(function ($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->first_response_at);
            });
            $avgResponseMinutes = round($totalMinutes / $tickets->count(), 1);
        }

        // Teacher Rating (NELC 2.4.9)
        $avgTeacherRating = \App\Models\TeacherRating::where('is_approved', true)->avg('overall_rating');

        // Attendance Rate (NELC 1.2.5)
        $totalAttendances = Attendance::count();
        $presentAttendances = Attendance::where('attended', true)->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 0;

        // Activity Logs Stats
        $activityStats = [
            'total' => ActivityLog::count(),
            'last_24h' => ActivityLog::where('created_at', '>=', now()->subDay())->count(),
            'xapi_synced' => ActivityLog::where('xapi_sent', true)->count(),
        ];

        // xAPI Stats
        $xapiStats = [
            'total' => XapiStatement::count(),
            'sent' => XapiStatement::where('status', 'sent')->count(),
            'pending' => XapiStatement::where('status', 'pending')->count(),
            'failed' => XapiStatement::where('status', 'failed')->count(),
        ];

        return [
            'satisfaction_rate' => $satisfactionRate,
            'avg_response_time_minutes' => $avgResponseMinutes,
            'teacher_rating' => round($avgTeacherRating, 2),
            'attendance_rate' => $attendanceRate,
            'activity_logs' => $activityStats,
            'xapi_statements' => $xapiStats,
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_programs' => Program::count(),
        ];
    }

    /**
     * Export as JSON
     */
    public function exportJson(array $data, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
        ];

        return response()->stream(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, 200, $headers);
    }

    /**
     * Export as CSV
     */
    public function exportCsv(array $data, array $headers, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $httpHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($data, $headers) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        }, 200, $httpHeaders);
    }

    /**
     * Export as PDF
     */
    public function exportPdf(string $view, array $data, string $filename): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
            ]);

        return $pdf->download("{$filename}.pdf");
    }
}
