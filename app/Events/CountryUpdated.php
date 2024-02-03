<?php

namespace App\Events;

class CountryUpdated
{
    /**
     * The new country.
     */
    public string $country;

    /**
     * Create a new event instance.
     */
    public function __construct(string $country)
    {
        $this->country = $country;
    }
}
