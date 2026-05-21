<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        $subjectIds = Subject::where(function ($q) use ($student) {
                $q->where('program_id', $student->program_id)
                  ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
            })->pluck('id');

        // Homeworks from subjects
        $subjectHomeworks = Homework::whereHas('session', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->with(['session.subject'])
            ->orderByDesc('created_at')
            ->get();

        // Homeworks from programs (course/training/english)
        $programHomeworks = Homework::whereHas('session', fn($q) => $q->where('program_id', $student->program_id))
            ->with(['session.program'])
            ->orderByDesc('created_at')
            ->get();

        $homeworks = $subjectHomeworks->merge($programHomeworks)->sortByDesc('created_at')->values();

        // Load student's own submissions
        $mySubmissions = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworks->pluck('id'))
            ->get()
            ->keyBy('homework_id');

        return view('student.homework.index', compact('homeworks', 'mySubmissions'));
    }

    public function submit(Request $request, Homework $homework)
    {
        $student = auth()->user();

        $request->validate([
            'content' => 'nullable|string|max:3000',
            'file'    => 'nullable|file|max:20480',
        ]);

        if (!$request->filled('content') && !$request->hasFile('file')) {
            return back()->withErrors(['content' => 'يرجى كتابة نص الإجابة أو رفع ملف.']);
        }

        $data = [
            'homework_id'  => $homework->id,
            'student_id'   => $student->id,
            'content'      => $request->content,
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('homework-submissions', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        HomeworkSubmission::updateOrCreate(
            ['homework_id' => $homework->id, 'student_id' => $student->id],
            $data
        );

        return back()->with('success', 'تم تسليم الواجب بنجاح ✓');
    }

    public function deleteSubmission(HomeworkSubmission $submission)
    {
        if ($submission->student_id !== auth()->id()) abort(403);

        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }
        $submission->delete();

        return back()->with('success', 'تم حذف التسليم');
    }
}
