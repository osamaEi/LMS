<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Program;

class ProgramController extends Controller
{
    public function index()
    {
        return response()->json(Program::where('status', 'active')->get());
    }

    public function show(Program $program)
    {
        return response()->json($program);
    }
}
