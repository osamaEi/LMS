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
}
