<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherRating;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherRatingController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get subjects where student can rate the teacher
        // (enrolled and not yet rated)
        $ratableSubjects = Subject::whereHas('enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'active');
            })
            ->whereDoesntHave('teacherRatings', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('teacher')
            ->get();

        // Get submitted ratings
        $submittedRatings = TeacherRating::where('student_id', $student->id)
            ->with(['teacher', 'subject'])
            ->latest()
            ->get();

        return view('student.teacher-ratings.index', compact('ratableSubjects', 'submittedRatings'));
    }

    public function create(Subject $subject)
    {
        $student = auth()->user();

        // Verify student is enrolled
        $isEnrolled = $subject->enrollments()
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.teacher-ratings.index')
                ->with('error', 'لست مسجلاً في هذه المادة');
        }

        // Check if already rated
        $alreadyRated = TeacherRating::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->exists();

        if ($alreadyRated) {
            return redirect()->route('student.teacher-ratings.index')
                ->with('info', 'لقد قمت بتقييم هذا المدرب مسبقاً');
        }

        $subject->load('teacher');

        return view('student.teacher-ratings.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $student = auth()->user();

        $validated = $request->validate([
            'knowledge_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'punctuality_rating' => 'required|integer|min:1|max:5',
            'support_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Calculate overall rating
        $overallRating = round((
            $validated['knowledge_rating'] +
            $validated['communication_rating'] +
            $validated['punctuality_rating'] +
            $validated['support_rating']
        ) / 4, 2);

        TeacherRating::create([
            'teacher_id' => $subject->teacher_id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'overall_rating' => $overallRating,
            'knowledge_rating' => $validated['knowledge_rating'],
            'communication_rating' => $validated['communication_rating'],
            'punctuality_rating' => $validated['punctuality_rating'],
            'support_rating' => $validated['support_rating'],
            'comment' => $validated['comment'],
            'is_approved' => false, // Requires admin approval
        ]);

        return redirect()->route('student.teacher-ratings.index')
            ->with('success', 'شكراً لك! تم إرسال تقييمك وسيتم مراجعته');
    }
}
