<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE programs MODIFY COLUMN type ENUM('diploma','training','certificate','course','english') NULL");

        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->nullable()->after('course_type');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('level');
        });

        DB::statement("ALTER TABLE programs MODIFY COLUMN type ENUM('diploma','training','certificate','course') NULL");
    }
};
