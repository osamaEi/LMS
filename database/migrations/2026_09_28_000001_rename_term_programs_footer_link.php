<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('footer_links')
            ->where('label_ar', 'برامج الترم')
            ->update(['label_ar' => 'الدبلومات', 'label_en' => 'Diplomas', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('footer_links')
            ->where('label_ar', 'الدبلومات')
            ->where('section', 'services')
            ->update(['label_ar' => 'برامج الترم', 'label_en' => 'Term Programs', 'updated_at' => now()]);
    }
};
