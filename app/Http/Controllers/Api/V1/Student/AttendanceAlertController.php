<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\AttendanceLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceAlertController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless($request->user()->role === 'student', 403);

        $alerts = [];
        if (AttendanceLimitService::isEnabled()) {
            // Attendance assignments identify the student's subjects, including
            // subjects assigned without a separate enrollment record.
            $subjects = Subject::whereIn('id', DB::table('attendances')
                ->join('class_sessions', 'class_sessions.id', '=', 'attendances.session_id')
                ->where('attendances.student_id', $request->user()->id)
                ->whereNull('class_sessions.deleted_at')
                ->select('class_sessions.subject_id'))
                ->orderBy('id')->get(['id', 'name_ar', 'name_en']);

            foreach ($subjects as $subject) {
                $status = AttendanceLimitService::statusFor($request->user()->id, $subject->id);
                $remaining = max(0, (int) floor($status['total'] * $status['limit'] / 100) - $status['absent']);
                if ($status['exempt'] || $status['absent'] === 0 || (!$status['blocked'] && $remaining > 1)) {
                    continue;
                }

                $alerts[] = [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'severity' => $status['blocked'] ? 'danger' : 'warning',
                    'code' => $status['blocked'] ? 'absence_limit_exceeded' : 'absence_limit_approaching',
                    'title' => $status['blocked'] ? 'تحذير أخير بخصوص الحضور' : 'تنبيه بخصوص الحضور',
                    'message' => $status['blocked']
                        ? 'لقد تجاوزت حد الغياب المسموح في هذه المادة، وتم منعك من حضور محاضراتها. يرجى مراجعة الإدارة.'
                        : 'انتبه! أنت على وشك تجاوز الحد المسموح للغياب في هذه المادة. يرجى الحرص على الحضور.',
                    'total_sessions' => $status['total'],
                    'absent_sessions' => $status['absent'],
                    'excused_sessions' => $status['excused'],
                    'absence_percent' => $status['percent'],
                    'allowed_absence_percent' => $status['limit'],
                    'remaining_allowed_absences' => $remaining,
                    'blocked' => $status['blocked'],
                ];
            }
        }

        return response()->json(['success' => true, 'data' => [
            'has_alerts' => count($alerts) > 0,
            'alerts' => $alerts,
        ]]);
    }
}
