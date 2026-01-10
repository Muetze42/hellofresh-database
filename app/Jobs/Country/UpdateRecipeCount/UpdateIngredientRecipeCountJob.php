<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Ingredient;

/**
 * Updates recipe counts for ingredients.
 */
class UpdateIngredientRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Ingredient::class;
    }
}
