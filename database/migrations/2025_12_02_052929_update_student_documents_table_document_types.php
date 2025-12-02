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
        Schema::table('student_documents', function (Blueprint $table) {
            DB::statement("ALTER TABLE student_documents MODIFY COLUMN document_type ENUM('cv', 'academic_certificate', 'national_id', 'photo', 'certificate', 'payment_billing', 'other') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            DB::statement("ALTER TABLE student_documents MODIFY COLUMN document_type ENUM('cv', 'academic_certificate', 'national_id', 'photo', 'other') NOT NULL");
        });
    }
};
