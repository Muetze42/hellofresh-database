<?php

namespace App\Livewire\Portal\Resources;

use App\Models\Tag;

/**
 * Livewire component for listing tags.
 */
class TagsIndex extends AbstractResourceIndex
{
    protected function getModelClass(): string
    {
        return Tag::class;
    }

    protected function getResourceTitle(): string
    {
        return 'Tags';
    }
}
