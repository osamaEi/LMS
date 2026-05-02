<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqData = Faq::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = array_merge(
            ['all' => ['label' => 'الكل', 'icon' => '🔍']],
            Faq::categories()
        );

        $catCounts = ['all' => $faqData->count()];
        foreach ($faqData as $faq) {
            $catCounts[$faq->category] = ($catCounts[$faq->category] ?? 0) + 1;
        }

        return view('front.faq', compact('faqData', 'categories', 'catCounts'));
    }
}
