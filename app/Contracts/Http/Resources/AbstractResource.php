<?php

namespace App\Contracts\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AbstractResource extends JsonResource
{
    public static bool $isIndex = false;

    /**
     * Create a new anonymous resource collection for the index.
     */
    public static function indexCollection(mixed $resource): AnonymousResourceCollection
    {
        static::$isIndex = true;

        return static::collection($resource);
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        if (static::$isIndex) {
            return $this->toIndexArray($request);
        }

        return $this->toShowArray($request);
    }

    /**
     * Transform the resource into an array.
     */
    abstract public function toShowArray(Request $request): array;

    /**
     * Transform the resource into an array for an index collection.
     */
    abstract public function toIndexArray(Request $request): array;
}
