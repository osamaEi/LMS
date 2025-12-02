<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'type')) {
                $table->enum('type', ['diploma', 'training'])->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'program_id')) {
                $table->foreignId('program_id')->nullable()->after('type')->constrained('programs')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'date_of_register')) {
                $table->date('date_of_register')->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('users', 'is_terms')) {
                $table->boolean('is_terms')->default(false)->after('date_of_register');
            }
            if (!Schema::hasColumn('users', 'is_confirm_user')) {
                $table->boolean('is_confirm_user')->default(false)->after('is_terms');
            }
            if (!Schema::hasColumn('users', 'profile_completed_at')) {
                $table->timestamp('profile_completed_at')->nullable()->after('is_confirm_user');
            }
        });

        // Add indexes separately to avoid conflicts
        $indexes = DB::select("SHOW INDEX FROM users WHERE Column_name IN ('program_id', 'type', 'gender')");
        $existingIndexes = array_column($indexes, 'Column_name');

        Schema::table('users', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('program_id', $existingIndexes)) {
                $table->index('program_id');
            }
            if (!in_array('type', $existingIndexes)) {
                $table->index('type');
            }
            if (!in_array('gender', $existingIndexes)) {
                $table->index('gender');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['program_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['gender']);

            $table->dropForeign(['program_id']);
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'type',
                'program_id',
                'date_of_register',
                'is_terms',
                'is_confirm_user',
                'profile_completed_at',
            ]);
        });
    }
};
