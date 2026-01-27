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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('installment_id')->nullable()->constrained('payment_installments')->onDelete('set null');

            $table->decimal('amount', 10, 2);
            $table->enum('type', ['payment', 'refund', 'adjustment']);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'tamara', 'waived']);

            $table->string('transaction_reference')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('success');

            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['payment_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
