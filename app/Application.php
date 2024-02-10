<?php

namespace App;

use App\Events\CountryUpdated;
use App\Models\Country;
use Illuminate\Foundation\Application as App;

class Application extends App
{
    /**
     * Register Country instance with the container.
     */
    public function setCountry(Country $country): void
    {
        $this['events']->dispatch(new CountryUpdated($country));

        $this->bind('country', fn () => $country);
    }

    /**
     * Get the available Country instance.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function country(): ?Country
    {
        return $this->has('country') ? $this->get('country') : null;
    }
}
