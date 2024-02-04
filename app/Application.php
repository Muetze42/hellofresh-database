<?php

namespace App;

use App\Events\CountryUpdated;
use Illuminate\Foundation\Application as App;
use Illuminate\Support\Str;

class Application extends App
{
    /**
     * Set the current application country.
     */
    public function setCountry(string $country): void
    {
        $this['config']->set('app.country', $country);

        $this['events']->dispatch(new CountryUpdated($country));
    }

    /**
     * Get the current application country.
     */
    public function getCountry(): string
    {
        return $this['config']->get('app.country');
    }

    /**
     * Set the current HelloFresh base URL.
     */
    public function setHelloFreshBaseUrl(string $country): void
    {
        $this['config']->set('app.hf_base_url', $country);
    }

    /**
     * Get the current HelloFresh base URL.
     */
    public function getHelloFreshBaseUrl(): string
    {
        return $this['config']->get('app.hf_base_url');
    }

    /**
     * Set the current HelloFresh API take value.
     */
    public function setHelloFreshApiTake(int $take): void
    {
        $this['config']->set('app.hf_api_take', $take);
    }

    /**
     * Get the current HelloFresh API take value.
     */
    public function getHelloFreshApiTake(int $default = 50): int
    {
        return $this['config']->get('app.hf_api_take', $default);
    }

    /**
     * Get table prefix for country tables.
     */
    public function getCountryPrefix(): string
    {
        return Str::lower($this->getCountry()) . '__' . $this->getLocale() . '_';
    }
}
