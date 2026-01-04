<?php

namespace App\Http\Middleware\Localization;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        abort_if(! $route || ! $route->hasParameter('locale') || ! $route->hasParameter('country'), 404);

        $countryCode = $route->parameter('country');
        $locale = $route->parameter('locale');

        abort_if(! is_string($countryCode) || ! is_string($locale) || strlen($countryCode) !== 2, 404);

        // Redirect legacy lowercase URLs to canonical uppercase format (e.g., /de-de → /de-DE)
        $canonicalCountryCode = strtoupper($countryCode);
        $canonicalLocale = strtolower($locale);

        if ($countryCode !== $canonicalCountryCode || $locale !== $canonicalLocale) {
            $path = preg_replace(
                '#^/' . preg_quote($locale . '-' . $countryCode, '#') . '#i',
                '/' . $canonicalLocale . '-' . $canonicalCountryCode,
                $request->getPathInfo()
            );

            return redirect($path . ($request->getQueryString() ? '?' . $request->getQueryString() : ''), 301);
        }

        $country = $this->requestedCountry($countryCode, $locale);

        $this->bindCountryContext($country, $locale);

        $route->forgetParameter('locale');
        $route->forgetParameter('country');

        return $next($request);
    }

    /**
     * Bind the country context to the application.
     */
    public function bindCountryContext(Country $country, string $locale): void
    {
        $this->setAppLocale($country, $locale);
        $this->setFallbackLocale($country, $locale);

        app()->instance('current.country', $country);
        Number::useLocale($locale);
    }

    /**
     * Set the current application fallback locale.
     */
    protected function setFallbackLocale(Country $country, string $locale): void
    {
        $countryLocales = array_values(
            Arr::where($country->locales, static fn (string $value): bool => $value !== $locale)
        );

        App::setFallbackLocale($countryLocales[0] ?? 'en');
    }

    /**
     * Set the current application locale.
     */
    protected function setAppLocale(Country $country, string $locale): void
    {
        App::setLocale($locale);
    }

    /**
     * Retrieves the requested country based on the provided country code and locale.
     */
    protected function requestedCountry(string $countryCode, string $locale): Country
    {
        $country = Country::active()->where('code', $countryCode)->first();

        abort_unless($country instanceof Country, 404);
        abort_unless(in_array($locale, $country->locales, true), 404);

        return $country;
    }
}
