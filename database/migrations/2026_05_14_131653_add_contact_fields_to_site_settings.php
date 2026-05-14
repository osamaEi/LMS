<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        foreach (['fax', 'sms_number', 'maps_url'] as $key) {
            DB::table('site_settings')->insertOrIgnore([
                'key'        => $key,
                'value'      => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['fax', 'sms_number', 'maps_url'])->delete();
    }
};
