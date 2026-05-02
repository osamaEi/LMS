<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $categories = Faq::categories();
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_ar' => 'required|string|max:500',
            'question_en' => 'nullable|string|max:500',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'nullable|string',
            'category'    => 'required|in:registration,courses,certificates,platform,support',
            'sort_order'  => 'nullable|integer|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function edit(Faq $faq)
    {
        $categories = Faq::categories();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question_ar' => 'required|string|max:500',
            'question_en' => 'nullable|string|max:500',
            'answer_ar'   => 'required|string',
            'answer_en'   => 'nullable|string',
            'category'    => 'required|in:registration,courses,certificates,platform,support',
            'sort_order'  => 'nullable|integer|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم حذف السؤال بنجاح');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update([
            'status' => $faq->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'تم تحديث حالة السؤال بنجاح');
    }
}
