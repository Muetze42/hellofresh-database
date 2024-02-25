<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Support\Requests\FilterRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Build a filtered query for recipes.
     */
    protected function recipesFilterQuery(Recipe|Builder $recipes, Request $request): Builder
    {
        $recipes->active();
        $filter = FilterRequest::parse($request);

        if ($filter['pdf']) {
            $recipes->whereNotNull('card_link');
        }

        if (!empty($filter['ingredients'])) {
            $recipes->whereHas('ingredients', function ($query) use ($filter) {
                $query->whereIn('id', $filter['ingredients']);
            }, count: $filter['iMode'] ? count($filter['ingredients']) : 1);

            $filter['ingredients'] = Ingredient::whereIn('id', $filter['ingredients'])
                ->get(['name', 'id']);
        }

        foreach (Arr::except($filter, ['pdf', 'iMode', 'ingredients']) as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $relation = explode('_', $key)[0];
            if (str_ends_with($key, '_except')) {
                $recipes->whereDoesntHave($relation, fn ($query) => $query->whereIn('id', $value));
            } else {
                $recipes->whereHas($relation, fn ($query) => $query->whereIn('id', $value));
            }
        }

        Inertia::share('filter', $filter);

        return $recipes;
    }
}
