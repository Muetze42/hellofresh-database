<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Label
 */
class LabelCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = LabelResource::class;
}
