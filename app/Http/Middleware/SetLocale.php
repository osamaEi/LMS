<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: ?lang= query param → Accept-Language header → session → default ar
        $locale = $request->query('lang')
            ?? $this->parseAcceptLanguage($request->header('Accept-Language'))
            ?? Session::get('locale')
            ?? 'ar';

        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    private function parseAcceptLanguage(?string $header): ?string
    {
        if (!$header) return null;
        $primary = strtolower(substr(trim(explode(',', $header)[0]), 0, 2));
        return in_array($primary, ['ar', 'en']) ? $primary : null;
    }
}
