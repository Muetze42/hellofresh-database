<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $locales = require storage_path('languages.php');

        return Inertia::render('Home/Index', [
            'countries' => Country::active()->orderBy('code')
                ->get(['code', 'locales'])
                ->map(fn (Country $country) => [
                    'country' => __('country.' . $country->code),
                    'code' => $country->code,
                    'locales' => array_map(
                        fn ($locale) => [
                            'locale' => $locale,
                            'lang' => Str::ucfirst(data_get($locales, $locale, $locale)),
                        ],
                        $country->locales
                    ),
                ]),
        ]);
    }
}
