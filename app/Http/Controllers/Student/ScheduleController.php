<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get all sessions for student's enrolled subjects
        $sessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('subject')
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'start' => $session->scheduled_at,
                    'end' => $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->addHours(2) : null,
                    'backgroundColor' => $this->getStatusColor($session->status),
                    'borderColor' => $this->getStatusColor($session->status),
                    'url' => route('admin.sessions.show', $session->id),
                    'extendedProps' => [
                        'subject' => $session->subject->name,
                        'type' => $session->type,
                        'status' => $session->status,
                        'session_number' => $session->session_number,
                    ]
                ];
            });

        return view('student.schedule', compact('sessions'));
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'live' => '#EF4444',      // Red
            'completed' => '#10B981',  // Green
            'scheduled' => '#3B82F6',  // Blue
            default => '#6B7280',      // Gray
        };
    }
}
