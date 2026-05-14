<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class FooterSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::allKeyed();
        $quickLinks    = FooterLink::where('section', 'quick')->orderBy('sort_order')->get();
        $serviceLinks  = FooterLink::where('section', 'services')->orderBy('sort_order')->get();
        return view('admin.footer-settings.index', compact('settings', 'quickLinks', 'serviceLinks'));
    }

    public function update(Request $request)
    {
        $keys = [
            'footer_description_ar','footer_description_en',
            'phone','email','fax','sms_number','maps_url',
            'address_ar','address_en',
            'working_hours_ar','working_hours_en',
            'copyright_ar','copyright_en',
            'social_twitter','social_instagram','social_linkedin',
            'social_youtube','social_facebook','social_snapchat','social_whatsapp',
        ];

        foreach ($keys as $key) {
            SiteSetting::set($key, $request->input($key));
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function storeLink(Request $request)
    {
        $request->validate([
            'label_ar'   => 'required|string|max:100',
            'label_en'   => 'nullable|string|max:100',
            'url'        => 'required|string|max:255',
            'section'    => 'required|in:quick,services',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        FooterLink::create([
            'label_ar'   => $request->label_ar,
            'label_en'   => $request->label_en,
            'url'        => $request->url,
            'section'    => $request->section,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return back()->with('success', 'تم إضافة الرابط');
    }

    public function destroyLink(FooterLink $link)
    {
        $link->delete();
        return back()->with('success', 'تم حذف الرابط');
    }

    public function toggleLink(FooterLink $link)
    {
        $link->update(['is_active' => !$link->is_active]);
        return back();
    }
}
