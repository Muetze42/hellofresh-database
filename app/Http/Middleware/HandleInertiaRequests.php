<?php

namespace App\Http\Middleware;

use App\Support\Support;
use Illuminate\Http\Request;
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
            'translations' => $this->getJsonTranslations(),
            'locale' => app()->getLocale(),
            'config' => config('application'),
            'country' => country()?->append('route')->only(['code', 'domain', 'route']),
            'support' => (new Support())->toArray(),
        ]);
    }

    /**
     * Load the messages for the current locale.
     */
    protected function getJsonTranslations(): array
    {
        return app('translator')
            ->getLoader()
            ->load(app()->getLocale(), '*', '*');
    }
}
