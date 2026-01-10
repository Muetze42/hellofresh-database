<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Label;

/**
 * Livewire component for listing labels.
 */
class LabelsIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Label::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Labels';
    }

    protected function getViewName(): string
    {
        return 'portal::livewire.resources.labels-index';
    }
}
