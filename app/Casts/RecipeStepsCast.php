<?php

namespace App\Casts;

use App\Casts\Traits\AttributeAsJsonSetterTrait;
use App\ValueObjects\RecipeStepsObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RecipeStepsCast implements CastsAttributes
{
    use AttributeAsJsonSetterTrait;

    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): RecipeStepsObject
    {
        return new RecipeStepsObject($value);
    }
}
