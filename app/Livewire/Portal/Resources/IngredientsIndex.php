<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Ingredient;

/**
 * Livewire component for listing ingredients.
 */
class IngredientsIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Ingredient::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Ingredients';
    }
}
