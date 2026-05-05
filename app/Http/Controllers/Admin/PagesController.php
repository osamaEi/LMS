<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug'       => 'required|string|unique:pages,slug|max:100|regex:/^[a-z0-9\-]+$/',
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'category'   => 'required|string|max:50',
        ]);

        Page::create([
            'slug'         => $request->slug,
            'title_ar'     => $request->title_ar,
            'title_en'     => $request->title_en ?? $request->title_ar,
            'content_ar'   => $request->content_ar,
            'content_en'   => $request->content_en ?? $request->content_ar,
            'category'     => $request->category,
            'is_published' => $request->boolean('is_published', true),
            'version'      => 1,
            'published_at' => now(),
            'updated_by'   => auth()->id(),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'تم إضافة الصفحة بنجاح');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'slug'       => 'required|string|max:100|unique:pages,slug,' . $page->id . '|regex:/^[a-z0-9\-]+$/',
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'category'   => 'required|string|max:50',
        ]);

        $page->update([
            'slug'         => $request->slug,
            'title_ar'     => $request->title_ar,
            'title_en'     => $request->title_en ?? $request->title_ar,
            'content_ar'   => $request->content_ar,
            'content_en'   => $request->content_en ?? $request->content_ar,
            'category'     => $request->category,
            'is_published' => $request->boolean('is_published', true),
            'version'      => $page->version + 1,
            'updated_by'   => auth()->id(),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'تم حذف الصفحة');
    }
}
