<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ConsentService;

class ConsentController extends Controller
{
    public function __invoke()
    {
        return response()->json(['success' => true, 'data' => [
            'version' => ConsentService::VERSION,
            'registration' => [
                ['field' => 'is_terms', 'required' => true, 'default' => false, 'text' => ConsentService::BASIC],
                ['field' => 'marketing_consent', 'required' => false, 'default' => false, 'text' => ConsentService::MARKETING],
            ],
            'enrollment' => [
                'field' => 'certificate_consent', 'required' => true,
                'default' => false, 'text' => ConsentService::CERTIFICATE,
            ],
            'links' => [
                'terms' => route('page.show', 'terms'),
                'privacy' => route('page.show', 'privacy-policy'),
            ],
        ]]);
    }
}
