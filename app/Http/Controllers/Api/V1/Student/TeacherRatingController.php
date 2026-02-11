<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherRating;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherRatingController extends Controller
{
    /**
     * GET /api/v1/student/teacher-ratings
     * List ratable subjects and submitted ratings
     */
    public function index()
    {
        $student = auth()->user();

        $ratableSubjects = Subject::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('status', 'active');
        })
            ->whereDoesntHave('teacherRatings', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('teacher:id,name,email,profile_photo')
            ->get();

        $submittedRatings = TeacherRating::where('student_id', $student->id)
            ->with(['teacher:id,name', 'subject:id,name_ar,name_en'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'ratable_subjects' => $ratableSubjects,
                'submitted_ratings' => $submittedRatings,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/teacher-ratings/{subjectId}
     * Show rating form data for a subject
     */
    public function show($subjectId)
    {
        $student = auth()->user();

        $subject = Subject::with('teacher:id,name,email,profile_photo')->findOrFail($subjectId);

        $isEnrolled = $subject->enrollments()
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'لست مسجلاً في هذه المادة',
            ], 403);
        }

        $alreadyRated = TeacherRating::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->exists();

        if ($alreadyRated) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بتقييم هذا المدرب مسبقاً',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => $subject,
                'rating_categories' => [
                    'knowledge_rating' => 'المعرفة والخبرة',
                    'communication_rating' => 'التواصل والشرح',
                    'punctuality_rating' => 'الالتزام بالمواعيد',
                    'support_rating' => 'الدعم والمساعدة',
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/student/teacher-ratings/{subjectId}
     * Submit a teacher rating
     */
    public function store(Request $request, $subjectId)
    {
        $student = auth()->user();

        $subject = Subject::findOrFail($subjectId);

        $isEnrolled = $subject->enrollments()
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'لست مسجلاً في هذه المادة',
            ], 403);
        }

        $alreadyRated = TeacherRating::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->exists();

        if ($alreadyRated) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بتقييم هذا المدرب مسبقاً',
            ], 422);
        }

        $validated = $request->validate([
            'knowledge_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'punctuality_rating' => 'required|integer|min:1|max:5',
            'support_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $overallRating = round((
            $validated['knowledge_rating'] +
            $validated['communication_rating'] +
            $validated['punctuality_rating'] +
            $validated['support_rating']
        ) / 4, 2);

        $rating = TeacherRating::create([
            'teacher_id' => $subject->teacher_id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'overall_rating' => $overallRating,
            'knowledge_rating' => $validated['knowledge_rating'],
            'communication_rating' => $validated['communication_rating'],
            'punctuality_rating' => $validated['punctuality_rating'],
            'support_rating' => $validated['support_rating'],
            'comment' => $validated['comment'],
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً لك! تم إرسال تقييمك وسيتم مراجعته',
            'data' => $rating,
        ], 201);
    }
}
