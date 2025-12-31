<?php

namespace App\Http\Resources\Api;

/**
 * @see \App\Models\Menu
 */
class MenuCollection extends AbstractApiResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = MenuResource::class;
}
