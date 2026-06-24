<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Teachers set a single personal Zoom join link here. It applies automatically
     * to every session they teach (schedule + student view) — see Session::zoom_join_url accessor.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('zoom_join_url', 500)->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('zoom_join_url');
        });
    }
};
