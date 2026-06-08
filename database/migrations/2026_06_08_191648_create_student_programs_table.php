<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('approved');
            $table->foreignId('class_id')->nullable()->constrained('program_classes')->nullOnDelete();
            $table->unsignedTinyInteger('current_term_number')->default(1);
            $table->date('enrolled_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_programs');
    }
};
