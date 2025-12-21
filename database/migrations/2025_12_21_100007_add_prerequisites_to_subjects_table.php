<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->text('prerequisites_description')->nullable()->after('description');
            $table->text('objectives')->nullable()->after('prerequisites_description');
            $table->text('target_audience')->nullable()->after('objectives');
            $table->string('language')->default('ar')->after('target_audience');
            $table->enum('delivery_mode', ['online', 'blended', 'in_person'])->default('online')->after('language');
        });

        // Create pivot table for subject prerequisites
        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('prerequisite_id')->constrained('subjects')->onDelete('cascade');
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();

            $table->unique(['subject_id', 'prerequisite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_prerequisites');

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'prerequisites_description',
                'objectives',
                'target_audience',
                'language',
                'delivery_mode'
            ]);
        });
    }
};
