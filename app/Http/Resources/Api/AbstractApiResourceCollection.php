<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractApiResourceCollection extends ResourceCollection
{
    /**
     * Customize the pagination information for the resource.
     *
     * @param  array<string, mixed>  $paginated
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        unset($default['links'], $default['meta']['links'], $default['meta']['path']);

        return $default;
    }
}
