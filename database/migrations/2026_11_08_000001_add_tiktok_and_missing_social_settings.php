<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $rows = [
        ['key' => 'social_tiktok',   'label' => 'تيك توك',  'description' => 'رابط حساب تيك توك'],
        ['key' => 'social_snapchat', 'label' => 'سناب شات', 'description' => 'رابط حساب سناب شات'],
        ['key' => 'social_whatsapp', 'label' => 'واتساب',   'description' => 'رابط واتساب (مثال: https://wa.me/9665xxxxxxxx)'],
    ];

    public function up(): void
    {
        foreach ($this->rows as $row) {
            // Reuse any value already saved from the footer settings page
            $existing = DB::table('site_settings')->where('key', $row['key'])->value('value');

            DB::table('settings')->insertOrIgnore([
                'key'         => $row['key'],
                'value'       => $existing ?? '',
                'group'       => 'social',
                'type'        => 'text',
                'label'       => $row['label'],
                'description' => $row['description'],
                'is_public'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'social_tiktok')->delete();
    }
};
