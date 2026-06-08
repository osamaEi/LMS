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
        $student    = auth()->user();
        $programIds = $student->allProgramIds();

        // Subjects where student can rate (enrolled directly OR in any of their programs via term)
        $ratableSubjects = Subject::where(function ($q) use ($student, $programIds) {
                $q->whereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id))
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds));
            })
            ->whereNotNull('teacher_id')
            ->whereDoesntHave('teacherRatings', fn($q) => $q->where('student_id', $student->id))
            ->with(['teacher', 'term.program'])
            ->get();

        // Submitted ratings
        $submittedRatings = TeacherRating::where('student_id', $student->id)
            ->with(['teacher', 'subject.term.program'])
            ->latest()
            ->get();

        return view('student.teacher-ratings.index', compact('ratableSubjects', 'submittedRatings'));
    }

    public function create(Subject $subject)
    {
        $student = auth()->user();

        // Verify student can access this subject (enrolled or in same program)
        $canAccess = $subject->where('id', $subject->id)
            ->where(function ($q) use ($student) {
                $q->where('program_id', $student->program_id)
                  ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
            })->exists();

        if (!$canAccess) {
            return redirect()->route('student.teacher-ratings.index')
                ->with('error', 'لست مسجلاً في هذه المقرر ');
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
