<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PendingUserController extends Controller
{
    /**
     * Display pending user registrations
     */
    public function index(Request $request)
    {
        $query = User::where('status', 'pending')
            ->where('role', 'student');

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pending-users.index', compact('users'));
    }

    /**
     * Show details of a pending user
     */
    public function show(User $user)
    {
        if ($user->status !== 'pending') {
            return redirect()->route('admin.pending-users.index')
                ->with('error', 'هذا المستخدم ليس قيد الانتظار');
        }

        return view('admin.pending-users.show', compact('user'));
    }

    /**
     * Approve a pending user
     */
    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'هذا المستخدم ليس قيد الانتظار');
        }

        $user->update(['status' => 'active']);

        return redirect()->route('admin.pending-users.index')
            ->with('success', 'تم قبول المستخدم بنجاح');
    }

    /**
     * Reject/Delete a pending user
     */
    public function reject(User $user)
    {
        if ($user->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'هذا المستخدم ليس قيد الانتظار');
        }

        $user->delete();

        return redirect()->route('admin.pending-users.index')
            ->with('success', 'تم رفض وحذف المستخدم بنجاح');
    }

    /**
     * Bulk approve users
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)
            ->where('status', 'pending')
            ->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'تم قبول المستخدمين المحددين بنجاح');
    }

    /**
     * Bulk reject users
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)
            ->where('status', 'pending')
            ->delete();

        return redirect()->back()
            ->with('success', 'تم رفض وحذف المستخدمين المحددين بنجاح');
    }
}
