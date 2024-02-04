<?php

namespace App\Http\Middleware;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CountryMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $name = $request->route()->parameter('country_lang');

        if ($name && substr_count($name, '-') === 1) {
            [$country, $locale] = explode('-', $name);

            if ($country = Country::whereCountry(Str::upper($country))->first()) {
                if (in_array($locale, $country->locales)) {
                    $country->switch($locale);

                    return $next($request);
                }
            }
        }

        abort(404);
    }
}
