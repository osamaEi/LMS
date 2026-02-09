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
        Schema::create('xapi_statements', function (Blueprint $table) {
            $table->uuid('id')->primary();        // xAPI requires UUID
            $table->foreignId('activity_log_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('verb_id');             // e.g. 'http://adlnet.gov/expapi/verbs/completed'
            $table->string('verb_display');        // e.g. 'completed'
            $table->string('object_type');         // 'Activity', 'Agent', 'SubStatement'
            $table->string('object_id');           // IRI of the activity
            $table->string('object_name')->nullable();
            $table->json('statement_json');        // Full xAPI statement as JSON
            $table->string('status', 20)->default('pending'); // 'pending', 'sent', 'failed', 'retry'
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('verb_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xapi_statements');
    }
};
