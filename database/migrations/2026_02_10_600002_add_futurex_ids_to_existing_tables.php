<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('futurex_course_id')->nullable()->after('id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('futurex_module_id')->nullable()->after('id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('futurex_enrollment_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('futurex_course_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('futurex_module_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('futurex_enrollment_id');
        });
    }
};
