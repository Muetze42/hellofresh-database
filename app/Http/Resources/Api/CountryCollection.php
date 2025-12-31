<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Country
 */
class CountryCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = CountryResource::class;
}
