<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LangController extends Controller
{
    public function switch($locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale', 'ar');
        }

        // Store in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);

        // Redirect back to previous page
        return redirect()->back();
    }
}
