<?php

namespace App\Contracts\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait HasIndexCollectionTrait
{
    /**
     * Determine if this resource has an index collection.
     */
    public static bool $isIndex = false;

    /**
     * Create a new anonymous resource collection for the index.
     */
    public static function indexCollection(mixed $resource): AnonymousResourceCollection
    {
        static::$isIndex = true;

        return static::collection($resource);
    }
}
