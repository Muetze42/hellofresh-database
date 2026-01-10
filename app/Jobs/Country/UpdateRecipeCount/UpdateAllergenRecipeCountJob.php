<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Allergen;

/**
 * Updates recipe counts for allergens.
 */
class UpdateAllergenRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Allergen::class;
    }
}
