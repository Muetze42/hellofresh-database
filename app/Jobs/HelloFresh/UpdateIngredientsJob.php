<?php

namespace App\Jobs\HelloFresh;

use App\Contracts\Jobs\AbstractCountryUpdateJob;
use App\Models\Family;
use App\Models\Ingredient;

class UpdateIngredientsJob extends AbstractCountryUpdateJob
{
    /**
     * Execute the job.
     *
     * @throws \NormanHuth\HellofreshScraper\Exceptions\HellofreshScraperException
     */
    public function handleCountry(): void
    {
        $response = $this->client->ingredients($this->skip);
        foreach ($response->items() as $item) {
            /* @var \App\Models\Ingredient $ingredient */
            $ingredient = $this->country->ingredients()->updateOrCreate(
                ['id' => $item->getKey()],
                Ingredient::freshAttributes($item)
            );

            $ingredientFamily = $item->family();

            if (!$ingredientFamily) {
                continue;
            }

            $family = Family::updateOrCreate(
                ['id' => $ingredientFamily->getKey()],
                Family::freshAttributes($ingredientFamily)
            );

            $ingredient->family()->associate($family);
        }

        $this->afterCountryHandle($response);
    }
}
