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

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('question_ar', 'like', "%{$term}%")
                  ->orWhere('question_en', 'like', "%{$term}%")
                  ->orWhere('answer_ar',   'like', "%{$term}%")
                  ->orWhere('answer_en',   'like', "%{$term}%");
            });
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
