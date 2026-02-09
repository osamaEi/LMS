<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('futurex_id')->nullable()->unique()->after('national_id');
            $table->string('sso_provider')->nullable()->after('futurex_id');
            $table->json('sso_metadata')->nullable()->after('sso_provider');
            $table->timestamp('sso_linked_at')->nullable()->after('sso_metadata');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['futurex_id', 'sso_provider', 'sso_metadata', 'sso_linked_at']);
        });
    }
};
