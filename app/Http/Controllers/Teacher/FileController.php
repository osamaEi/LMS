<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();

        $subjects = Subject::assignedToTeacher($teacher->id)
            ->with([
                'files',
                'sessions' => function ($q) {
                    $q->with(['files' => function ($fq) {
                        $fq->whereIn('type', ['pdf', 'video'])->orderBy('order');
                    }])->orderBy('session_number');
                },
            ])
            ->get();

        $activeSubjectId = $request->integer('subject');

        return view('teacher.files.index', compact('subjects', 'activeSubjectId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'title'      => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file'       => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,png,jpg,jpeg',
        ]);

        $subject = Subject::where('id', $request->subject_id)
            ->assignedToTeacher(auth()->id())
            ->firstOrFail();

        $uploaded = $request->file('file');
        $path = $uploaded->store('subject-files/' . $subject->id, 'public');

        SubjectFile::create([
            'subject_id'         => $subject->id,
            'title'              => $request->title,
            'description'        => $request->description,
            'file_path'          => $path,
            'file_original_name' => $uploaded->getClientOriginalName(),
            'file_size'          => $uploaded->getSize(),
            'file_type'          => strtolower($uploaded->getClientOriginalExtension()),
            'order'              => SubjectFile::where('subject_id', $subject->id)->max('order') + 1,
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح');
    }

    public function destroy(SubjectFile $file)
    {
        $subject = Subject::where('id', $file->subject_id)
            ->assignedToTeacher(auth()->id())
            ->firstOrFail();

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }
}
