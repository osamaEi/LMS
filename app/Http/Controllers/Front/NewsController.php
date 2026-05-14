<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::active()->latest('published_at')->paginate(9);
        return view('front.news', compact('news'));
    }

    public function show(News $news)
    {
        abort_if($news->status !== 'active', 404);
        $related = News::active()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->limit(3)
            ->get();
        return view('front.news-show', compact('news', 'related'));
    }
}
