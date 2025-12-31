<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Ingredient
 */
class IngredientCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = IngredientResource::class;
}
