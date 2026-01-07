<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Get teacher's subjects for the create modal
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->get();

        // Get all sessions for the teacher's subjects
        $sessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with(['subject', 'subject.teacher'])
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'start' => $session->scheduled_at,
                    'end' => $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->addHours(2) : null,
                    'backgroundColor' => $this->getStatusColor($session->status),
                    'borderColor' => $this->getStatusColor($session->status),
                    'textColor' => '#ffffff',
                    'url' => route('teacher.my-subjects.show', $session->subject->id),
                    'extendedProps' => [
                        'subject' => $session->subject->name,
                        'subject_id' => $session->subject->id,
                        'teacher' => $session->subject->teacher->name,
                        'teacher_id' => $session->subject->teacher_id,
                        'type' => $session->type,
                        'status' => $this->getStatusLabel($session->status),
                        'session_number' => $session->session_number,
                        'description' => $session->description,
                    ]
                ];
            });

        return view('teacher.schedule', compact('sessions', 'subjects'));
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'live' => '#EF4444',      // Red - Live
            'completed' => '#10B981',  // Green - Completed
            'scheduled' => '#3B82F6',  // Blue - Scheduled
            default => '#6B7280',      // Gray - Other
        };
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'live' => 'مباشر',
            'completed' => 'مكتملة',
            'scheduled' => 'مجدولة',
            default => 'أخرى',
        };
    }
}
