<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Ingredient;
use Override;

/**
 * Activates ingredients based on recipe count.
 */
class ActivateIngredientsJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Ingredient::class;
    }

    #[Override]
    protected function getMinimumRecipeCount(): int
    {
        return 0;
    }
}
