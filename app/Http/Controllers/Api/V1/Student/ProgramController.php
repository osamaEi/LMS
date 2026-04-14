<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * GET /api/v1/student/my-program
     * Show student's program details
     */
    public function show()
    {
        $student = auth()->user();
        $program = $student->program;

        // ─── No program ────────────────────────────────────────────────────────
        if (!$program) {
            return response()->json([
                'success' => true,
                'data'    => new StudentProgramResource(['status' => 'no_program']),
            ]);
        }

        // ─── Pending ───────────────────────────────────────────────────────────
        if ($student->program_status === 'pending') {
            return response()->json([
                'success' => true,
                'data'    => new StudentProgramResource([
                    'status'  => 'pending',
                    'program' => $program,
                ]),
            ]);
        }

        // ─── Enrolled ──────────────────────────────────────────────────────────
        $currentTerm = $student->current_term_number
            ? $program->terms()->where('term_number', $student->current_term_number)->first()
            : $program->terms()->orderBy('term_number')->first();

        $currentTermNumber = $currentTerm?->term_number ?? 1;

        return response()->json([
            'success' => true,
            'data'    => new StudentProgramResource([
                'status'       => 'enrolled',
                'program'      => $program,
                'current_term' => $currentTermNumber,
                'current_term_name' => $currentTerm?->name,
            ]),
        ]);
    }

    /**
     * POST /api/v1/student/enroll-program
     * Enroll in a program
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $student = auth()->user();

        if ($student->program_id) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مسجل بالفعل في برنامج دراسي',
            ], 409);
        }

        $program = Program::where('id', $request->program_id)
            ->where('status', 'active')
            ->firstOrFail();

        $student->update([
            'program_id' => $program->id,
            'program_status' => 'pending',
            'current_term_number' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب التسجيل في البرنامج: ' . $program->name . '. في انتظار موافقة الإدارة.',
        ]);
    }
}
