<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Utensil;

/**
 * Activates utensils based on recipe count.
 */
class ActivateUtensilsJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Utensil::class;
    }
}
