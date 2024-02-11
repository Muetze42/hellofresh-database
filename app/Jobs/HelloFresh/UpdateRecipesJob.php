<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryUpdateJob;
use App\Services\HelloFreshService;

class UpdateRecipesJob extends AbstractCountryUpdateJob
{
    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->recipes($this->skip);
        foreach ($response->items() as $item) {
            HelloFreshService::syncRecipe($item);
        }

        $this->afterCountryHandle($response);
    }
}
