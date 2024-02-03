<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use NormanHuth\HellofreshScraper\Models\HelloFreshTime;

class PrepTimeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return new HelloFreshTime($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof HelloFreshTime) {
            return $value->toString();
        }

        return $value;
    }
}
