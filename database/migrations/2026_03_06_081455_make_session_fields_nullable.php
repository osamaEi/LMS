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
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->change();
            $table->string('title_en')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->integer('session_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('title_ar')->nullable(false)->change();
            $table->string('title_en')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->integer('session_number')->nullable(false)->change();
        });
    }
};
