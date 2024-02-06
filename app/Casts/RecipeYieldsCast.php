<?php

namespace App\Casts;

use App\Casts\Traits\AttributeAsJsonSetterTrait;
use App\ValueObjects\RecipeYieldsObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RecipeYieldsCast implements CastsAttributes
{
    use AttributeAsJsonSetterTrait;

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): RecipeYieldsObject
    {
        return new RecipeYieldsObject($value);
    }
}
