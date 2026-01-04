<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Allergen;

/**
 * Activates allergens based on recipe count.
 */
class ActivateAllergensJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Allergen::class;
    }
}
