<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Allergen;

/**
 * Livewire component for listing allergens.
 */
class AllergensIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Allergen::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Allergens';
    }
}
