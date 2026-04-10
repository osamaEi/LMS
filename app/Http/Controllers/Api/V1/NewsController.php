<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * GET /api/v1/news
     */
    public function index(Request $request)
    {
        $news = News::active()
            ->latest('published_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data'    => NewsResource::collection($news),
            'meta'    => [
                'page' => $news->currentPage(),
                'last_page'    => $news->lastPage(),
                'per_page'     => $news->perPage(),
                'total'        => $news->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/news/{id}
     */
    public function show(News $news)
    {
        if (!$news->isActive()) {
            return response()->json(['success' => false, 'message' => 'الخبر غير متاح'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new NewsResource($news),
        ]);
    }
}
