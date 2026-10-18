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
        return $this->enterDashboard($request, $student, 'student');
    }

    public function storeTeacher(Request $request, User $teacher)
    {
        return $this->enterDashboard($request, $teacher, 'teacher');
    }

    private function enterDashboard(Request $request, User $user, string $role)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'super_admin'], true), 403);
        abort_if($request->session()->has('student_dashboard_admin_id'), 403);
        abort_unless($user->role === $role, 404);
        abort_unless($user->status === 'active', 403);

        $request->session()->put('student_dashboard_admin_id', $request->user()->id);
        $request->session()->put('dashboard_access_role', $role);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($role.'.dashboard');
    }

    public function destroy(Request $request)
    {
        $returnRoute = $request->session()->get('dashboard_access_role') === 'teacher'
            ? 'admin.teachers.index' : 'admin.students.index';
        app(\App\Services\StudentDashboardAccessService::class)->restoreAdmin($request);

        return redirect()->route($returnRoute);
    }
}
