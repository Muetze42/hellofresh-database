<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractLocalizedController extends Controller
{
    /**
     * Get the current country from the request context.
     */
    protected function country(): Country
    {
        /** @var Country $country */
        $country = resolve('current.country');

        return $country;
    }

    /**
     * Apply sorting to the query based on the request.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function applySorting(Builder $query, Request $request): Builder
    {
        $sort = $request->input('sort', 'created_at');

        return $query->orderByDesc(
            in_array($sort, ['created_at', 'updated_at'], true) ? $sort : 'created_at'
        );
    }
}
