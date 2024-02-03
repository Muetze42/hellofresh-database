<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CanActivateTrait
{
    /**
     * Bootstrap the model and its traits.
     */
    public static function bootCanActivateTrait(): void
    {
        //static::creating(function ($model) {});
    }

    /**
     * Initialize the trait.
     */
    protected function initializeCanActivateTrait(): void
    {
        $this->mergeFillable(['active']);
        $this->mergeCasts(['active' => 'bool']);
    }

    /**
     * Scope a query to only include active resources.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }
}
