<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Label;

/**
 * Updates recipe counts for labels.
 */
class UpdateLabelRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Label::class;
    }
}
