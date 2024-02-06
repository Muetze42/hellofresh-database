<?php

namespace App\Casts;

use App\Casts\Traits\AttributeAsJsonSetterTrait;
use App\ValueObjects\RecipeNutritionObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RecipeNutritionCast implements CastsAttributes
{
    use AttributeAsJsonSetterTrait;

    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): RecipeNutritionObject
    {
        return new RecipeNutritionObject($value);
    }
}
