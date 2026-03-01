<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE student_documents MODIFY COLUMN document_type ENUM('cv', 'academic_certificate', 'national_id', 'national_id_front', 'national_id_back', 'photo', 'certificate', 'payment_billing', 'other') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE student_documents MODIFY COLUMN document_type ENUM('cv', 'academic_certificate', 'national_id', 'photo', 'certificate', 'payment_billing', 'other') NOT NULL");
    }
};
