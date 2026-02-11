<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * GET /api/v1/student/schedule
     * Calendar events for student's sessions
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = Session::whereHas('subject.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->with('subject:id,name_ar,name_en');

        // Optional date range filter
        if ($request->has('start')) {
            $query->where('scheduled_at', '>=', $request->query('start'));
        }
        if ($request->has('end')) {
            $query->where('scheduled_at', '<=', $request->query('end'));
        }

        $sessions = $query->get()->map(function ($session) {
            return [
                'id' => $session->id,
                'title' => $session->title,
                'start' => $session->scheduled_at,
                'end' => $session->scheduled_at ? Carbon::parse($session->scheduled_at)->addHours(2) : null,
                'color' => $this->getStatusColor($session->status),
                'subject' => $session->subject->name_ar ?? $session->subject->name,
                'type' => $session->type,
                'status' => $session->status,
                'session_number' => $session->session_number,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            'live' => '#EF4444',
            'completed' => '#10B981',
            'scheduled' => '#3B82F6',
            default => '#6B7280',
        };
    }
}
