<?php

use App\Models\Setting;
use App\Services\AttendanceLimitService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::updateOrCreate(['key' => AttendanceLimitService::SETTING_ENABLED], [
            'value' => '1', 'type' => 'boolean', 'group' => 'attendance', 'label' => 'تفعيل حد الغياب',
        ]);
        Setting::firstOrCreate(['key' => AttendanceLimitService::SETTING_PERCENT], [
            'value' => '20', 'type' => 'number', 'group' => 'attendance', 'label' => 'نسبة الغياب المسموحة',
        ]);
        Setting::clearCache();
    }

    public function down(): void
    {
        // Keep administrator-owned settings when rolling back application code.
    }
};
