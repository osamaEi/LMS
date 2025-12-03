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
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('banner_photo')->nullable()->after('description');
            $table->integer('total_hours')->nullable()->after('credits')->comment('إجمالي ساعات الفيديوهات');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('specialization')->nullable()->after('bio')->comment('تخصص المدرس');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['banner_photo', 'total_hours']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('specialization');
        });
    }
};
