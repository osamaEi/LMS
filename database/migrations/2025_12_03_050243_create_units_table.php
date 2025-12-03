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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title'); // عنوان الوحدة
            $table->text('description')->nullable(); // وصف الوحدة
            $table->integer('unit_number')->comment('رقم الوحدة في المادة'); // 1, 2, 3...
            $table->integer('duration_hours')->nullable()->comment('المدة المتوقعة بالساعات');
            $table->text('learning_objectives')->nullable()->comment('أهداف التعلم');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('order')->default(0)->comment('ترتيب العرض');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('subject_id');
            $table->index(['subject_id', 'unit_number']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
