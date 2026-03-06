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
        Schema::table('programs', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->change();
            $table->string('name_en')->nullable()->change();
            $table->string('code')->nullable()->change();
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable()->change();
            $table->integer('term_number')->nullable()->change();
            $table->string('name_ar')->nullable()->change();
            $table->string('name_en')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('name_ar')->nullable(false)->change();
            $table->string('name_en')->nullable(false)->change();
            $table->string('code')->nullable(false)->change();
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable(false)->change();
            $table->integer('term_number')->nullable(false)->change();
            $table->string('name_ar')->nullable(false)->change();
            $table->string('name_en')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
