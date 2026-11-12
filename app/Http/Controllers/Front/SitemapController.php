<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Program;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        $urls = [];
        $add = function (string $loc, $lastmod = null, string $freq = 'weekly', string $priority = '0.7') use (&$urls) {
            $urls[] = compact('loc', 'lastmod', 'freq', 'priority');
        };

        // Static pages
        $add(route('home'), null, 'daily', '1.0');
        foreach ([
            'training-paths', 'training-programs', 'short-courses', 'courses.developmental',
            'courses.qualifying', 'english-courses', 'offers', 'about', 'news', 'faq', 'contact',
        ] as $name) {
            $add(route($name), null, 'weekly', '0.8');
        }
        foreach ([
            'privacy', 'academic-integrity', 'intellectual-property', 'communication', 'attendance',
            'assessment', 'ai-ethics', 'technical-support', 'accessibility', 'national-compliance', 'risk-management',
        ] as $p) {
            $add(route('nelc.policies.' . $p), null, 'yearly', '0.3');
        }
        $add(route('nelc.guides.student'), null, 'monthly', '0.4');
        $add(route('nelc.guides.teacher'), null, 'monthly', '0.4');
        $add(route('nelc.open-content'), null, 'monthly', '0.5');

        // Programs — pick the public detail route that matches the program type
        Program::where('status', 'active')->get()->each(function ($program) use ($add) {
            $route = match (true) {
                $program->type === 'english' => 'english-courses.show',
                $program->type === 'course' && $program->course_type === 'qualifying' => 'courses.qualifying.show',
                $program->type === 'course' => 'courses.developmental.show',
                default => 'training-paths.show',
            };
            $add(route($route, $program), $program->updated_at, 'weekly', '0.9');
        });

        Offer::where('status', 'active')->get()->each(
            fn ($offer) => $add(route('offers.show', $offer), $offer->updated_at, 'weekly', '0.7')
        );

        News::where('status', 'active')->get()->each(
            fn ($news) => $add(route('news.show', $news), $news->updated_at, 'monthly', '0.6')
        );

        Page::where('is_published', true)->get()->each(
            fn ($page) => $add(route('page.show', $page->slug), $page->updated_at, 'monthly', '0.5')
        );

        $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $out .= "  <url>\n";
            $out .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            if ($u['lastmod']) {
                $out .= '    <lastmod>' . $u['lastmod']->toAtomString() . "</lastmod>\n";
            }
            $out .= "    <changefreq>{$u['freq']}</changefreq>\n";
            $out .= "    <priority>{$u['priority']}</priority>\n";
            $out .= "  </url>\n";
        }
        $out .= '</urlset>';

        return $out;
    }
}
