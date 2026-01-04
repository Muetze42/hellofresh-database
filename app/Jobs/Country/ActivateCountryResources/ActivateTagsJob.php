<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Models\Tag;

/**
 * Activates tags based on recipe count.
 */
class ActivateTagsJob extends AbstractActivateResourceJob
{
    protected function getModelClass(): string
    {
        return Tag::class;
    }
}
