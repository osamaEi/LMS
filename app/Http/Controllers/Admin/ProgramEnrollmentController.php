<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramEnrollmentController extends Controller
{
    /**
     * Display listing of program enrollments with status filter
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // all | pending | approved | none

        $query = User::where('role', 'student')
            ->with('program');

        // Status filter
        if ($status === 'pending') {
            $query->where('program_status', 'pending')->whereNotNull('program_id');
        } elseif ($status === 'approved') {
            $query->where('program_status', 'approved')->whereNotNull('program_id');
        } elseif ($status === 'none') {
            $query->where('program_status', 'none');
        } else {
            // Show all students that have ever had a program (including none/rejected)
            $query->where(function ($q) {
                $q->whereNotNull('program_id')
                  ->orWhere('program_status', 'approved');
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // Program filter
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $users = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();

        // Status counts for tabs
        $counts = [
            'all'      => User::where('role', 'student')->where(function ($q) {
                              $q->whereNotNull('program_id')->orWhere('program_status', 'approved');
                          })->count(),
            'pending'  => User::where('role', 'student')->where('program_status', 'pending')->whereNotNull('program_id')->count(),
            'approved' => User::where('role', 'student')->where('program_status', 'approved')->count(),
            'none'     => User::where('role', 'student')->where('program_status', 'none')->count(),
        ];

        // Programs for filter dropdown
        $programs = \App\Models\Program::where('status', 'active')->orderBy('name_ar')->get();

        return view('admin.program-enrollments.index', compact('users', 'status', 'counts', 'programs'));
    }

    /**
     * Display individual pending enrollment
     */
    public function show(User $user)
    {
        $user->load('program', 'track', 'documents');

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
