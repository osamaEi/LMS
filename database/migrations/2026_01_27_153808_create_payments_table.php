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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Amount details
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);

            // Payment configuration
            $table->enum('payment_type', ['full', 'installment']);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'tamara', 'waived'])->nullable();
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled', 'refunded'])->default('pending');

            // Tamara specific
            $table->string('tamara_checkout_id')->nullable()->unique();
            $table->string('tamara_order_id')->nullable();
            $table->json('tamara_metadata')->nullable();

            // Additional info
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'program_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
