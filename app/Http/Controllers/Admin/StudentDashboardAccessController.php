<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardAccessController extends Controller
{
    public function store(Request $request, User $student)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'super_admin'], true), 403);
        abort_if($request->session()->has('student_dashboard_admin_id'), 403);
        abort_unless($student->role === 'student', 404);
        abort_unless($student->status === 'active', 403);

        $request->session()->put('student_dashboard_admin_id', $request->user()->id);
        Auth::login($student);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    public function destroy(Request $request)
    {
        app(\App\Services\StudentDashboardAccessService::class)->restoreAdmin($request);

        return redirect()->route('admin.students.index');
    }
}
