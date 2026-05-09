<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Offer;

class OfferController extends Controller
{
    public function index()
    {
        $activeOffers   = Offer::with('program')->active()->orderBy('end_date')->get();
        $upcomingOffers = Offer::with('program')->upcoming()->orderBy('start_date')->get();

        return view('student.offers.index', compact('activeOffers', 'upcomingOffers'));
    }
}
