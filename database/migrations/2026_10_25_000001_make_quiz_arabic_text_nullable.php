<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Quizzes written in English no longer need an Arabic copy: the Arabic text
 * columns become optional, and either language satisfies validation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->change();
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->text('question_ar')->nullable()->change();
        });
        Schema::table('question_options', function (Blueprint $table) {
            $table->text('option_ar')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('quizzes')->whereNull('title_ar')->update(['title_ar' => DB::raw('title_en')]);
        DB::table('questions')->whereNull('question_ar')->update(['question_ar' => DB::raw("COALESCE(question_en, '')")]);
        DB::table('question_options')->whereNull('option_ar')->update(['option_ar' => DB::raw("COALESCE(option_en, '')")]);

        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('title_ar')->nullable(false)->change();
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->text('question_ar')->nullable(false)->change();
        });
        Schema::table('question_options', function (Blueprint $table) {
            $table->text('option_ar')->nullable(false)->change();
        });
    }
};
