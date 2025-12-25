<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SitemapController extends Controller
{
    /**
     * Serve the sitemap index listing all country/locale sitemaps.
     */
    public function index(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $countries = Country::query()
            ->active()
            ->orderBy('code')
            ->get(['code', 'locales', 'updated_at']);

        foreach ($countries as $country) {
            foreach ($country->locales as $locale) {
                $filename = sprintf('sitemap-%s-%s.xml', $locale, $country->code);

                if (! Storage::disk('public')->exists('sitemaps/' . $filename)) {
                    continue;
                }

                $lastMod = Storage::disk('public')->lastModified('sitemaps/' . $filename);

                $xml .= sprintf(
                    "  <sitemap>\n    <loc>%s</loc>\n    <lastmod>%s</lastmod>\n  </sitemap>\n",
                    url(sprintf('sitemap/%s-%s.xml', $locale, $country->code)),
                    Date::createFromTimestamp($lastMod)->format('Y-m-d')
                );
            }
        }

        $xml .= '</sitemapindex>';

        return response($xml, SymfonyResponse::HTTP_OK, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Serve an individual sitemap for a locale/country combination.
     */
    public function show(string $locale, string $country): Response
    {
        $filename = sprintf('sitemap-%s-%s.xml', $locale, $country);

        abort_unless(Storage::disk('public')->exists('sitemaps/' . $filename), SymfonyResponse::HTTP_NOT_FOUND);

        $content = Storage::disk('public')->get('sitemaps/' . $filename);

        return response($content, SymfonyResponse::HTTP_OK, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
