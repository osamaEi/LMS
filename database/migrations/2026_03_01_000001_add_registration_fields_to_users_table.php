<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'specialization_type')) {
                $table->string('specialization_type')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('users', 'date_of_graduation')) {
                $table->date('date_of_graduation')->nullable()->after('specialization_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['specialization_type', 'date_of_graduation']);
        });
    }
};
