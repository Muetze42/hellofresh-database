<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Cuisine;

/**
 * Activates cuisines based on recipe count.
 */
class ActivateCuisinesJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Cuisine::class;
    }
}
