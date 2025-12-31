<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Allergen
 */
class AllergenCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AllergenResource::class;
}
