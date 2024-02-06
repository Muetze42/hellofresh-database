<?php

namespace App\Casts;

use App\ValueObjects\RecipeStepsObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class RecipeStepsCast implements CastsAttributes
{
    use HasAttributes;

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): RecipeStepsObject
    {
        return new RecipeStepsObject($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value) {
            return $this->castAttributeAsJson($key, $value);
        }

        return null;
    }
}
