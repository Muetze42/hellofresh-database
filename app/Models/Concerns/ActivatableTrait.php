<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait ActivatableTrait
{
    /**
     * Initialize the trait.
     */
    protected function initializeActivatableTrait(): void
    {
        $this->mergeFillable(['active']);
        $this->mergeCasts(['active' => 'bool']);
    }

    /**
     * Scope a query to only include active resources.
     *
     * @param  Builder<$this>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', true);
    }
}
