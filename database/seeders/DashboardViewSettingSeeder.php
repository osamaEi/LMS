<?php

namespace App\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DashboardViewSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teacher dashboard view setting if it doesn't exist
        Setting::firstOrCreate(
            ['key' => 'teacher_dashboard_view'],
            [
                'key' => 'teacher_dashboard_view',
                'value' => 'teacher.dashboard',
                'group' => 'dashboard',
                'type' => 'select',
                'options' => ['teacher.dashboard' => 'العرض الكامل', 'teacher.dashboard-simple' => 'العرض المبسط'],
                'label' => 'نمط عرض لوحة المعلم',
                'description' => 'اختر نمط العرض المفضل لكل معلم: العرض الكامل بكل التفاصيل أو العرض المبسط',
                'is_public' => false,
            ]
        );
    }
}
