<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryUpdateJob;
use App\Models\Allergen;

class UpdateAllergensJob extends AbstractCountryUpdateJob
{
    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->allergens($this->skip);

        foreach ($response->items() as $item) {
            Allergen::freshUpdateOrCreate($item);
        }

        if ($this->limit && ($response->skip() + $response->take()) >= $this->limit) {
            return;
        }

        if ($next = $response->getNextPaginate()) {
            static::countryDispatch($this->limit, $next);
        }
    }
}
