<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group')->map(function ($group) {
            return $group->toArray();
        })->toArray();

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle file uploads
                if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    $path = $file->store('settings', 'public');
                    $value = $path;

                    // Delete old file
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                }

                // Handle boolean values
                if ($setting->type === 'boolean') {
                    $value = $value === 'on' || $value === '1' || $value === true ? '1' : '0';
                }

                $setting->update(['value' => $value]);
            }
        }

        Setting::clearCache();

        return redirect()->route('admin.settings')
            ->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function updateGroup(Request $request, string $group)
    {
        $settings = $request->except('_token', '_method');

        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->where('group', $group)->first();

            if ($setting) {
                if ($setting->type === 'boolean') {
                    $value = $value === 'on' || $value === '1' || $value === true ? '1' : '0';
                }

                $setting->update(['value' => $value]);
            }
        }

        Setting::clearCache();

        return redirect()->route('admin.settings')
            ->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function clearCache()
    {
        Setting::clearCache();
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        return redirect()->route('admin.settings')
            ->with('success', 'تم مسح ذاكرة التخزين المؤقت بنجاح');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Mail::raw('هذا بريد تجريبي من نظام إدارة التعلم', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('بريد تجريبي - نظام إدارة التعلم');
            });

            return response()->json(['success' => true, 'message' => 'تم إرسال البريد التجريبي بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'فشل إرسال البريد: ' . $e->getMessage()], 500);
        }
    }

    public function maintenance(Request $request)
    {
        $enable = $request->boolean('enable');

        if ($enable) {
            Artisan::call('down', ['--secret' => 'admin-bypass-' . time()]);
        } else {
            Artisan::call('up');
        }

        Setting::set('maintenance_mode', $enable ? '1' : '0');

        return response()->json([
            'success' => true,
            'message' => $enable ? 'تم تفعيل وضع الصيانة' : 'تم إلغاء وضع الصيانة',
        ]);
    }
}
