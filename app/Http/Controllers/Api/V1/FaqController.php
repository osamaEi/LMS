<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::active()->orderBy('sort_order')->orderBy('id');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->get();

        $grouped = $faqs->groupBy('category');

        return response()->json([
            'success' => true,
            'data'    => FaqResource::collection($faqs),
            'grouped' => $grouped->map(fn($items) => FaqResource::collection($items)),
            'total'   => $faqs->count(),
        ]);
    }
}
