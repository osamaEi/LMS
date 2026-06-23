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
        Schema::table('payments', function (Blueprint $table) {
            foreach (['paytabs_tran_ref', 'paytabs_cart_id'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'paytabs_tran_ref')) {
                $table->string('paytabs_tran_ref')->nullable();
            }
            if (!Schema::hasColumn('payments', 'paytabs_cart_id')) {
                $table->string('paytabs_cart_id')->nullable();
            }
        });
    }
};
