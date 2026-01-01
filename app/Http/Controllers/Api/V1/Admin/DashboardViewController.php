<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardViewController extends Controller
{
    /**
     * Get available dashboard views
     */
    public function getAvailableViews()
    {
        return response()->json([
            'available_views' => [
                'teacher' => [
                    ['key' => 'teacher.dashboard', 'label' => 'العرض الكامل', 'description' => 'عرض شامل مع جميع الإحصائيات والرسوم البيانية'],
                    ['key' => 'teacher.dashboard-simple', 'label' => 'العرض المبسط', 'description' => 'عرض مبسط وسهل الاستخدام'],
                ],
                'student' => [
                    // Add more views as needed
                ],
                'admin' => [
                    // Add more views as needed
                ],
            ],
            'current_settings' => [
                'teacher_dashboard_view' => Setting::get('teacher_dashboard_view', 'teacher.dashboard'),
            ],
        ]);
    }

    /**
     * Update dashboard view setting
     */
    public function updateDashboardView(Request $request, string $role)
    {
        $validated = $request->validate([
            'view' => 'required|string',
        ]);

        $settingKey = "{$role}_dashboard_view";

        // Verify the view exists
        if (!view()->exists($validated['view'])) {
            return response()->json([
                'error' => 'View does not exist',
                'view' => $validated['view'],
            ], 404);
        }

        Setting::set($settingKey, $validated['view']);

        return response()->json([
            'message' => 'Dashboard view updated successfully',
            'setting_key' => $settingKey,
            'new_value' => $validated['view'],
        ], 200);
    }
}
