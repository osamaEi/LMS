<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('nafath_transactions');

        if (Schema::hasColumn('users', 'nafath_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nafath_verified_at');
            });
        }

        if (Schema::hasColumn('users', 'nafath_transaction_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nafath_transaction_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('nafath_verified_at')->nullable();
            $table->string('nafath_transaction_id')->nullable();
        });
    }
};
