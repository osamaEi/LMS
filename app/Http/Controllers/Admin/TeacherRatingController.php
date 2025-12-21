<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherRating;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherRatingController extends Controller
{
    public function index(Request $request)
    {
        // Get all teachers with their ratings
        $teachers = User::where('role', 'teacher')
            ->withCount(['ratingsReceived' => function($q) {
                $q->where('is_approved', true);
            }])
            ->get()
            ->map(function($teacher) {
                $teacher->avg_rating = $teacher->getAverageRating();
                return $teacher;
            })
            ->sortByDesc('avg_rating');

        // Get pending ratings for approval
        $pendingRatings = TeacherRating::where('is_approved', false)
            ->with(['teacher', 'student', 'subject'])
            ->latest()
            ->paginate(10);

        return view('admin.teacher-ratings.index', compact('teachers', 'pendingRatings'));
    }

    public function show(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            abort(404);
        }

        $ratings = TeacherRating::where('teacher_id', $teacher->id)
            ->where('is_approved', true)
            ->with(['student', 'subject'])
            ->latest()
            ->paginate(15);

        $breakdown = $teacher->getRatingsBreakdown();

        // Ratings per subject
        $subjectRatings = TeacherRating::where('teacher_id', $teacher->id)
            ->where('is_approved', true)
            ->select('subject_id', DB::raw('AVG(overall_rating) as avg_rating'), DB::raw('COUNT(*) as count'))
            ->groupBy('subject_id')
            ->with('subject')
            ->get();

        return view('admin.teacher-ratings.show', compact('teacher', 'ratings', 'breakdown', 'subjectRatings'));
    }

    public function approve(TeacherRating $rating)
    {
        $rating->update(['is_approved' => true]);
        return back()->with('success', 'تم اعتماد التقييم بنجاح');
    }

    public function reject(TeacherRating $rating)
    {
        $rating->delete();
        return back()->with('success', 'تم رفض التقييم');
    }

    public function report()
    {
        // Overall statistics
        $stats = [
            'total_ratings' => TeacherRating::where('is_approved', true)->count(),
            'avg_overall' => round(TeacherRating::where('is_approved', true)->avg('overall_rating') ?? 0, 2),
            'avg_knowledge' => round(TeacherRating::where('is_approved', true)->avg('knowledge_rating') ?? 0, 2),
            'avg_communication' => round(TeacherRating::where('is_approved', true)->avg('communication_rating') ?? 0, 2),
            'avg_punctuality' => round(TeacherRating::where('is_approved', true)->avg('punctuality_rating') ?? 0, 2),
            'avg_support' => round(TeacherRating::where('is_approved', true)->avg('support_rating') ?? 0, 2),
        ];

        // Top rated teachers
        $topTeachers = User::where('role', 'teacher')
            ->get()
            ->map(function($teacher) {
                $teacher->avg_rating = $teacher->getAverageRating();
                $teacher->ratings_count = $teacher->ratingsReceived()->where('is_approved', true)->count();
                return $teacher;
            })
            ->filter(function($teacher) {
                return $teacher->ratings_count >= 3; // Minimum 3 ratings
            })
            ->sortByDesc('avg_rating')
            ->take(10);

        // Ratings distribution
        $distribution = TeacherRating::where('is_approved', true)
            ->select(DB::raw('FLOOR(overall_rating) as rating'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('FLOOR(overall_rating)'))
            ->pluck('count', 'rating');

        return view('admin.teacher-ratings.report', compact('stats', 'topTeachers', 'distribution'));
    }
}
