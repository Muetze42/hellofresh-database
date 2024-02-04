<?php

namespace App\Http\Clients;

use NormanHuth\HellofreshScraper\Http\Client;

class HelloFreshClient extends Client
{
    /**
     * Create a new HelleFresh API client instance.
     */
    public function __construct(
        string $isoCountryCode = null,
        string $isoLocale = null,
        int $take = null,
        string $baseUrl = null
    ) {
        if (!$take) {
            $take = country()?->take;
        }

        if (!$baseUrl) {
            $baseUrl = country()?->domain;
        }

        if (!$isoCountryCode) {
            $isoCountryCode = country()?->country;
        }

        if (!$isoLocale) {
            $isoLocale = country()?->locale;
        }

        parent::__construct($isoCountryCode, $isoLocale, $take, $baseUrl);
    }
}
