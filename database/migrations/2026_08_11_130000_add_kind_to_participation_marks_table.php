<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which report column a manual mark belongs to. Existing rows were all
     * participation items, so that stays the default.
     */
    public function up(): void
    {
        Schema::table('participation_marks', function (Blueprint $table) {
            $table->string('kind', 20)->default('participation')->after('subject_id');
            $table->index(['student_id', 'subject_id', 'kind'], 'pm_student_subject_kind_idx');
        }); 
    }

    public function down(): void
    {
        Schema::table('participation_marks', function (Blueprint $table) {
            $table->dropIndex('pm_student_subject_kind_idx');
            $table->dropColumn('kind');
        });
    }
};
