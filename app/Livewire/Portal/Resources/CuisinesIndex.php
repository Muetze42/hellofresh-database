<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Cuisine;

/**
 * Livewire component for listing cuisines.
 */
class CuisinesIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Cuisine::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Cuisines';
    }
}
