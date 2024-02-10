<?php

namespace App\Events;

use App\Models\Country;

class CountryUpdated
{
    /**
     * The new country.
     */
    public Country $country;

    /**
     * Create a new event instance.
     */
    public function __construct(Country $country)
    {
        $this->code = $country;
    }
}
