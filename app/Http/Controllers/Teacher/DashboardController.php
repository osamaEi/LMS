<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\User;
use App\Services\Teacher\TeacherDashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TeacherDashboardService $dashboardService,
    ) {}

    public function index()
    {
        $dashboardView = Setting::get('teacher_dashboard_view', 'teacher.dashboard');

        if (! view()->exists($dashboardView)) {
            $dashboardView = 'teacher.dashboard';
        }

        return view(
            $dashboardView,
            $this->dashboardService->data(auth()->user()),
        );
    }

    public function showSubject($id)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->with(['term.program', 'enrollments.student'])
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number')
            ->get();

        $students = User::whereHas('enrollments', function ($query) use ($id) {
            $query->where('subject_id', $id);
        })->get();

        return view('teacher.subject-detail', compact('subject', 'sessions', 'students'));
    }
}
