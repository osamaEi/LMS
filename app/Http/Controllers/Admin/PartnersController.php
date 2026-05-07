<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnersController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.partners.index', compact('partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'logo'       => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'url'        => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('logo')->store('uploads/partners', 'public');

        Partner::create([
            'name'       => $request->name,
            'logo'       => $path,
            'url'        => $request->url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return back()->with('success', 'تم إضافة الشريك بنجاح');
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'url'        => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name'       => $request->name,
            'url'        => $request->url,
            'sort_order' => $request->sort_order ?? $partner->sort_order,
        ];

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($partner->logo);
            $data['logo'] = $request->file('logo')->store('uploads/partners', 'public');
        }

        $partner->update($data);

        return back()->with('success', 'تم تحديث الشريك بنجاح');
    }

    public function toggle(Partner $partner)
    {
        $partner->update(['is_active' => !$partner->is_active]);
        return back()->with('success', $partner->is_active ? 'تم تفعيل الشريك' : 'تم إخفاء الشريك');
    }

    public function destroy(Partner $partner)
    {
        Storage::disk('public')->delete($partner->logo);
        $partner->delete();
        return back()->with('success', 'تم حذف الشريك بنجاح');
    }
}
