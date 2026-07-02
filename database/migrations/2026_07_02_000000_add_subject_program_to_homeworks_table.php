<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homeworks', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('session_id')
                ->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->after('subject_id')
                ->constrained('programs')->cascadeOnDelete();
        });

        // Homework is no longer required to belong to a session; make it optional.
        Schema::table('homeworks', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('homeworks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
