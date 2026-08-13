<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Manual overrides of the absence-limit ban: an admin can let a specific
     * student back into a specific subject's sessions even after they went over
     * the allowed percentage.
     */
    public function up(): void
    {
        Schema::create('attendance_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'subject_id']);
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_exemptions');
    }
};
