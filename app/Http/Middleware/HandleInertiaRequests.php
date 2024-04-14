<?php

namespace App\Http\Middleware;

use App\Models\Country;
use App\Models\Setting;
use App\Support\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app.page';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'translations' => $this->jsonTranslations(),
            'locale' => app()->getLocale(),
            'settings' => Setting::toArray(),
            'country' => fn () => country()?->append('route')->only(['code', 'domain', 'data', 'route']),
            'support' => (new Support())->toArray(),
            'countries' => $this->availableCountries(),
            'filterKey' => $request->input('filter'),
            'user' => fn () => $request->user()?->only(['id', 'name']),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
        ]);
    }

    public function availableCountries()
    {
        $locales = require storage_path('languages.php');

        return Country::active()->orderBy('code')
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
            ]);
    }

    /**
     * Load the messages for the current locale.
     */
    protected function jsonTranslations(): array
    {
        return app('translator')
            ->getLoader()
            ->load(app()->getLocale(), '*', '*');
    }
}
