<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Models\Tag;

/**
 * Updates recipe counts for tags.
 */
class UpdateTagRecipeCountJob extends AbstractUpdateRecipeCountJob
{
    protected function getModelClass(): string
    {
        return Tag::class;
    }
}
