<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Support\Requests\FilterRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
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

        if (!empty($filter['ingredients_not'])) {
            $recipes->whereDoesntHave('ingredients', function ($query) use ($filter) {
                $query->whereNotIn('id', $filter['ingredients_not']);
            });

            $filter['ingredients_not'] = Ingredient::whereIn('id', $filter['ingredients_not'])
                ->get(['name', 'id']);
        }

        if (!empty($filter['allergens'])) {
            $recipes->whereDoesntHave('allergens', function ($query) use ($filter) {
                $query->whereIn('id', $filter['allergens']);
            });

            $filter['allergens'] = Allergen::whereIn('id', $filter['allergens'])
                ->get(['name', 'id']);
        }

        Inertia::share('filter', $filter);

        return $recipes;
    }
}
