<?php

namespace App;

use App\Events\CountryUpdated;
use App\Models\Country;
use Illuminate\Foundation\Application as App;

class Application extends App
{
    /**
     * The Country container identifier.
     */
    protected string $countryAbstract = 'country';

    /**
     * Register Country instance with the container.
     */
    public function setCountry(Country $country): void
    {
        $this['events']->dispatch(new CountryUpdated($country));

        $this->bind($this->countryAbstract, fn () => $country);
    }

    /**
     * Get the available Country instance.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function country(): ?Country
    {
        return $this->has($this->countryAbstract) ? $this->get($this->countryAbstract) : null;
    }
}
