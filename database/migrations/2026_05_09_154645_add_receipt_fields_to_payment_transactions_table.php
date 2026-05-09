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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('notes');
            $table->enum('receipt_status', ['pending', 'approved', 'rejected'])->nullable()->after('receipt_path');
            $table->text('receipt_rejection_reason')->nullable()->after('receipt_status');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['receipt_path', 'receipt_status', 'receipt_rejection_reason']);
        });
    }
};
