<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('paytabs_cart_id');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_session_id');
        });

        // Add 'stripe' to payment_method enum
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'tamara', 'waived', 'paytabs', 'stripe') NULL");

        // Add 'stripe' and 'paytabs' to payment_transactions payment_method enum
        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'tamara', 'waived', 'paytabs', 'stripe') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent_id']);
        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'tamara', 'waived', 'paytabs') NULL");

        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'tamara', 'waived') NOT NULL");
    }
};
