<?php

namespace App\Livewire\Concerns;

use App\Http\Middleware\LocalizationMiddleware;
use App\Models\Country;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

trait WithLocalizedContextTrait
{
    #[Locked]
    public int $countryId;

    #[Locked]
    public string $locale;

    /**
     * Deprecated: Kept for backwards compatibility with existing Livewire snapshots.
     * Old snapshots contain this property and would fail to hydrate without it.
     *
     * @deprecated Remove after all user sessions have expired (typically 1-2 weeks)
     */
    #[Locked]
    public bool $localizedContextInitialized = false;

    /**
     * Boot the localized context.
     *
     * On initial requests: Middleware has run, so we can get country from there.
     * On subsequent requests: This runs before hydration, so countryId isn't available yet.
     */
    public function bootWithLocalizedContextTrait(): void
    {
        // On initial request, middleware has run and binding exists
        if (app()->bound('current.country')) {
            $country = current_country();
            $this->countryId = $country->id;
            $this->locale = app()->getLocale();
        }

        // On subsequent requests, countryId will be hydrated later
        // and hydrateWithLocalizedContextTrait will restore the binding
    }

    /**
     * Restore the localized context after hydration on subsequent requests.
     *
     * This runs AFTER properties are hydrated from the snapshot,
     * so $this->countryId is now available.
     */
    public function hydrateWithLocalizedContextTrait(): void
    {
        if (app()->bound('current.country')) {
            return;
        }

        $country = Country::findOrFail($this->countryId);

        resolve(LocalizationMiddleware::class)->bindCountryContext(
            $country,
            $this->locale
        );
    }

    /**
     * Get the current country.
     */
    #[Computed]
    public function country(): Country
    {
        return Country::findOrFail($this->countryId);
    }
}
