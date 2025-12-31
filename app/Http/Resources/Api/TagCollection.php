<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Tag
 */
class TagCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = TagResource::class;
}
