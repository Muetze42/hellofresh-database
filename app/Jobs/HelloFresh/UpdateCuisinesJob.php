<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryUpdateJob;
use App\Models\Cuisine;

class UpdateCuisinesJob extends AbstractCountryUpdateJob
{
    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->cuisines($this->skip);

        foreach ($response->items() as $item) {
            Cuisine::updateOrCreate(
                ['id' => $item->getKey()],
                Cuisine::freshAttributes($item)
            );
        }

        $this->afterCountryHandle($response);
    }
}
