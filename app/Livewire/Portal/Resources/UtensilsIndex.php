<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Utensil;

/**
 * Livewire component for listing utensils.
 */
class UtensilsIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Utensil::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Utensils';
    }
}
