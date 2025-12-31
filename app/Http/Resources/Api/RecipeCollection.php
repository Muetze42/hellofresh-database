<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Recipe
 */
class RecipeCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = RecipeCollectionResource::class;
}
