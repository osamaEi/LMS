<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_code', 20)->nullable()->unique()->after('id');
        });

        // Assign codes to all existing students ordered by id
        $students = DB::table('users')->where('role', 'student')->orderBy('id')->get(['id']);
        foreach ($students as $i => $student) {
            DB::table('users')->where('id', $student->id)->update([
                'student_code' => 'STU-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('student_code');
        });
    }
};
