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

        $perPage = (int) $request->get('per_page', 15);
        $faqs = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => FaqResource::collection($faqs),
            'pagination' => [
                'total'        => $faqs->total(),
                'per_page'     => $faqs->perPage(),
                'current_page' => $faqs->currentPage(),
                'last_page'    => $faqs->lastPage(),
                'from'         => $faqs->firstItem(),
                'to'           => $faqs->lastItem(),
                'next_page_url'=> $faqs->nextPageUrl(),
                'prev_page_url'=> $faqs->previousPageUrl(),
            ],
        ]);
    }
}
