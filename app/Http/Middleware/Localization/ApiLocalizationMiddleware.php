<?php

namespace App\Http\Middleware\Localization;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

class ApiLocalizationMiddleware
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

        $countryCode = strtoupper($countryCode);
        $locale = strtolower($locale);

        $country = $this->requestedCountry($countryCode, $locale);

        $this->bindCountryContext($country, $locale);

        $route->forgetParameter('locale');
        $route->forgetParameter('country');

        return $next($request);
    }

    /**
     * Bind the country context to the application.
     *
     * Note: We intentionally do NOT set App::setLocale() here.
     * The API should return error messages and validation messages in English.
     * Content localization is handled separately via the content locale binding.
     */
    protected function bindCountryContext(Country $country, string $locale): void
    {
        app()->instance('current.country', $country);
        app()->instance('current.content_locale', $locale);

        Number::useLocale($locale);
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
