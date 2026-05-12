<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE offers MODIFY COLUMN discount_type ENUM('percentage','fixed','override') NOT NULL DEFAULT 'percentage'");

        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('offer_price', 10, 2)->nullable()->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('offer_price');
        });

        DB::statement("ALTER TABLE offers MODIFY COLUMN discount_type ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage'");
    }
};
