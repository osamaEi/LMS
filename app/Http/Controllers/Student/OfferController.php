<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Offer;

class OfferController extends Controller
{
    public function index()
    {
        $activeOffers   = Offer::with('programs')->active()->orderByRaw('end_date IS NULL, end_date')->get();
        $upcomingOffers = Offer::with('programs')->upcoming()->orderBy('start_date')->get();

        return view('student.offers.index', compact('activeOffers', 'upcomingOffers'));
    }
}
