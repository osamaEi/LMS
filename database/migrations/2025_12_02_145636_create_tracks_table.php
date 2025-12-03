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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('name'); // اسم المسار
            $table->string('code')->unique(); // كود المسار
            $table->text('description')->nullable(); // وصف المسار
            $table->integer('total_terms')->default(10); // عدد الأرباع (10 أرباع)
            $table->integer('duration_months')->nullable(); // مدة المسار بالأشهر
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('program_id');
            $table->index('status');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
