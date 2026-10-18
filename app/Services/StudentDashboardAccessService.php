<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardAccessService
{
    public function restoreAdmin(Request $request): void
    {
        $adminId = $request->session()->get('student_dashboard_admin_id');
        abort_unless($adminId, 403);

        $admin = User::find($adminId);
        abort_unless($admin && in_array($admin->role, ['admin', 'super_admin'], true)
            && !in_array($admin->status, ['inactive', 'suspended'], true), 403);

        $request->session()->forget('student_dashboard_admin_id');
        Auth::login($admin);
        $request->session()->regenerate();
    }
}
