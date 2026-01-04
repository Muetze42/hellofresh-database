<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Label;

/**
 * Activates labels based on recipe count.
 */
class ActivateLabelsJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Label::class;
    }
}
