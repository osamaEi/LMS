<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('supervisor_name')->nullable()->after('supervisor_id');
        });

        DB::statement("ALTER TABLE programs MODIFY COLUMN type ENUM('diploma','training','developmental','qualifying','course') NULL");
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('supervisor_name');
        });

        DB::statement("ALTER TABLE programs MODIFY COLUMN type ENUM('diploma','training','certificate','course') NULL");
    }
};
