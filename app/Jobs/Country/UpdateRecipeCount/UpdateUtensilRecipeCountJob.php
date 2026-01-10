<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Utensil;

/**
 * Updates recipe counts for utensils.
 */
class UpdateUtensilRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Utensil::class;
    }
}
