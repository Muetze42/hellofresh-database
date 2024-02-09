<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Model;

trait CountrySluggableRouteTrait
{
    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where('country_id', country()->getKey())->where('id', explode('-', $value)[0])->firstOrFail();
    }
}
