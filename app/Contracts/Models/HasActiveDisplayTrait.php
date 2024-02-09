<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Builder;

trait HasActiveDisplayTrait
{
    /**
     * Scope a query to only include active resources.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('display_label', true);
    }
}
