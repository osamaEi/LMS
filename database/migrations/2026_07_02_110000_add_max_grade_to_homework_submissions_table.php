<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            // The grade denominator ("out of"), e.g. 4 out of 5. Defaults to 100
            // so existing grades keep their original meaning.
            $table->unsignedSmallInteger('max_grade')->default(100)->after('grade');
        });
    }

    public function down(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->dropColumn('max_grade');
        });
    }
};
