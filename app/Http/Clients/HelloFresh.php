<?php

namespace App\Http\Clients;

use Illuminate\Support\Facades\App;
use NormanHuth\HellofreshScraper\Http\Client;

class HelloFresh extends Client
{
    public function __construct(
        int $take = 10,
        string $baseUrl = null,
        string $isoCountryCode = null,
        string $isoLocale = null
    ) {
        if (!$baseUrl) {
            $baseUrl = App::getHelloFreshBaseUrl();
        }

        if (!$isoCountryCode) {
            $isoCountryCode = App::getCountry();
        }

        if (!$isoLocale) {
            $isoLocale = App::getLocale();
        }

        parent::__construct($isoCountryCode, $isoLocale, $take, $baseUrl);
    }
}
