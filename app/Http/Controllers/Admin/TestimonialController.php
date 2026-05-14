<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'author_ar'  => 'required|string|max:100',
            'author_en'  => 'nullable|string|max:100',
            'role_ar'    => 'nullable|string|max:150',
            'role_en'    => 'nullable|string|max:150',
            'text_ar'    => 'required|string|max:600',
            'text_en'    => 'nullable|string|max:600',
            'rating'     => 'required|integer|min:1|max:5',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')
                         ->with('success', 'تمت إضافة التقييم بنجاح.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'author_ar'  => 'required|string|max:100',
            'author_en'  => 'nullable|string|max:100',
            'role_ar'    => 'nullable|string|max:150',
            'role_en'    => 'nullable|string|max:150',
            'text_ar'    => 'required|string|max:600',
            'text_en'    => 'nullable|string|max:600',
            'rating'     => 'required|integer|min:1|max:5',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')
                         ->with('success', 'تم تحديث التقييم بنجاح.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'تم حذف التقييم.');
    }
}
