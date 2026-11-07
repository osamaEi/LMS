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
        $search = trim((string) $request->input('search', ''));
        $sort = $request->input('sort', 'rating');

        // Overall statistics
        $approved = TeacherRating::where('is_approved', true);
        $stats = [
            'total'       => (clone $approved)->count(),
            'pending'     => TeacherRating::where('is_approved', false)->count(),
            'avg'         => round((clone $approved)->avg('overall_rating') ?? 0, 2),
            'rated_teachers' => (clone $approved)->distinct('teacher_id')->count('teacher_id'),
            'teachers'    => User::where('role', 'teacher')->count(),
        ];

        // Teachers with aggregated ratings (single query, no N+1)
        $approvedFilter = fn ($q) => $q->where('is_approved', true);

        $teachersQuery = User::where('role', 'teacher')
            ->withCount(['ratingsReceived as ratings_count' => $approvedFilter])
            ->withAvg(['ratingsReceived as avg_rating' => $approvedFilter], 'overall_rating')
            ->withAvg(['ratingsReceived as avg_teaching' => $approvedFilter], 'teaching_quality')
            ->withAvg(['ratingsReceived as avg_communication' => $approvedFilter], 'communication')
            ->withAvg(['ratingsReceived as avg_punctuality' => $approvedFilter], 'punctuality')
            ->withAvg(['ratingsReceived as avg_delivery' => $approvedFilter], 'content_delivery')
            ->withCount(['ratingsReceived as pending_count' => fn ($q) => $q->where('is_approved', false)]);

        if ($search !== '') {
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        match ($sort) {
            'count' => $teachersQuery->orderByDesc('ratings_count'),
            'name'  => $teachersQuery->orderBy('name'),
            default => $teachersQuery->orderByDesc('avg_rating')->orderByDesc('ratings_count'),
        };

        $teachers = $teachersQuery->paginate(15, ['*'], 'teachers_page')->withQueryString();

        // Pending ratings awaiting approval
        $pendingRatings = TeacherRating::where('is_approved', false)
            ->with(['teacher:id,name', 'student:id,name', 'subject:id,name_ar,name_en'])
            ->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        return view('admin.teacher-ratings.index', compact('teachers', 'pendingRatings', 'stats', 'search', 'sort'));
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:teacher_ratings,id',
        ]);

        $query = TeacherRating::whereIn('id', $data['ids'])->where('is_approved', false);
        $count = $data['action'] === 'approve'
            ? $query->update(['is_approved' => true])
            : $query->delete();

        $msg = $data['action'] === 'approve' ? "تم اعتماد {$count} تقييم" : "تم رفض {$count} تقييم";

        return back()->with('success', $msg);
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
            'avg_knowledge' => round(TeacherRating::where('is_approved', true)->avg('teaching_quality') ?? 0, 2),
            'avg_communication' => round(TeacherRating::where('is_approved', true)->avg('communication') ?? 0, 2),
            'avg_punctuality' => round(TeacherRating::where('is_approved', true)->avg('punctuality') ?? 0, 2),
            'avg_support' => round(TeacherRating::where('is_approved', true)->avg('content_delivery') ?? 0, 2),
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
