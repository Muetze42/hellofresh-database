<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Cuisine;

/**
 * Updates recipe counts for cuisines.
 */
class UpdateCuisineRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Cuisine::class;
    }
}
