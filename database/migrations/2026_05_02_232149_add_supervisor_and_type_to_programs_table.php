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
            $table->enum('type', ['diploma', 'training', 'certificate'])->nullable()->after('image');
            $table->foreignId('supervisor_id')->nullable()->after('type')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['type', 'supervisor_id']);
        });
    }
};
