<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'nullable|string|max:255',
            'body_ar'      => 'required|string',
            'body_en'      => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:active,inactive',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم إضافة الخبر بنجاح');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'nullable|string|max:255',
            'body_ar'      => 'required|string',
            'body_en'      => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:active,inactive',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'تم حذف الخبر بنجاح');
    }

    public function toggleStatus(News $news)
    {
        $news->update([
            'status' => $news->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'تم تحديث حالة الخبر بنجاح');
    }
}
