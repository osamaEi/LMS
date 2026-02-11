<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramEnrollmentController extends Controller
{
    /**
     * Display listing of pending program enrollments
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student')
            ->where('program_status', 'pending')
            ->whereNotNull('program_id')
            ->with('program');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.program-enrollments.index', compact('users'));
    }

    /**
     * Display individual pending enrollment
     */
    public function show(User $user)
    {
        if ($user->program_status !== 'pending') {
            return redirect()->route('admin.program-enrollments.index')
                ->with('error', 'هذا الطالب ليس لديه طلب تسجيل معلق');
        }

        $user->load('program', 'track');

        return view('admin.program-enrollments.show', compact('user'));
    }

    /**
     * Approve program enrollment
     */
    public function approve(User $user)
    {
        if ($user->program_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'هذا الطالب ليس لديه طلب تسجيل معلق');
        }

        $user->update(['program_status' => 'approved']);

        return redirect()->route('admin.program-enrollments.index')
            ->with('success', 'تم قبول طلب التسجيل في البرنامج للطالب: ' . $user->name);
    }

    /**
     * Reject program enrollment
     */
    public function reject(User $user)
    {
        if ($user->program_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'هذا الطالب ليس لديه طلب تسجيل معلق');
        }

        // Reset program and status
        $user->update([
            'program_id' => null,
            'program_status' => 'none',
            'track_id' => null,
            'current_term_number' => null,
        ]);

        return redirect()->route('admin.program-enrollments.index')
            ->with('success', 'تم رفض طلب التسجيل في البرنامج للطالب: ' . $user->name);
    }

    /**
     * Bulk approve enrollments
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = User::whereIn('id', $request->user_ids)
            ->where('program_status', 'pending')
            ->update(['program_status' => 'approved']);

        return redirect()->route('admin.program-enrollments.index')
            ->with('success', "تم قبول {$count} طلب تسجيل بنجاح");
    }

    /**
     * Bulk reject enrollments
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = User::whereIn('id', $request->user_ids)
            ->where('program_status', 'pending')
            ->update([
                'program_id' => null,
                'program_status' => 'none',
                'track_id' => null,
                'current_term_number' => null,
            ]);

        return redirect()->route('admin.program-enrollments.index')
            ->with('success', "تم رفض {$count} طلب تسجيل بنجاح");
    }
}
