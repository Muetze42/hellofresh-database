<?php

namespace App\Livewire\Concerns;

use App\Http\Middleware\CountryMiddleware;
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
     * Boot the localized context from the middleware.
     */
    public function bootWithLocalizedContextTrait(): void
    {
        if (! isset($this->countryId)) {
            $country = current_country();
            $this->countryId = $country->id;
            $this->locale = app()->getLocale();

            return;
        }

        $this->restoreLocalizedContext();
    }

    /**
     * Restore the localized context for subsequent Livewire requests.
     */
    protected function restoreLocalizedContext(): void
    {
        if (app()->bound('current.country')) {
            return;
        }

        $country = Country::findOrFail($this->countryId);

        resolve(CountryMiddleware::class)->bindCountryContext(
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
