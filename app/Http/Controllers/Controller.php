<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
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

        foreach (Arr::except($filter, ['pdf', 'iMode', 'ingredients', 'prepTime', 'difficulties']) as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $relation = explode('_', $key)[0];
            if (str_ends_with($key, '_except')) {
                $recipes->whereDoesntHave($relation, fn ($query) => $query->whereIn('id', $value));
            } else {
                $recipes->whereHas($relation, fn ($query) => $query->whereIn('id', $value));
            }
            $model = 'App\Models\\' . Str::studly(Str::singular($relation));
            $filter[$key] = app($model)::whereIn('id', $value)->get(['name', 'id']);
        }

        if ($search = $request->input('search')) {
            $recipes->where(function (Builder $query) use ($search) {
                /* @var \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe $query */
                $query->whereId($search)
                    ->orWhereRaw('LOWER(name) like ?', ['%' . Str::lower($search) . '%'])
                    ->orWhereRaw('LOWER(description) like ?', ['%' . Str::lower($search) . '%'])
                ;
            });
        }

        if ($filter['prepTime'][0] != data_get(country()->data, 'prepMin', 0)) {
            $recipes->where('minutes', '>=', $filter['prepTime'][0]);
        }

        if ($filter['prepTime'][1] != data_get(country()->data, 'prepMin', 0)) {
            $recipes->where('minutes', '<=', $filter['prepTime'][1]);
        }

        $difficulties = array_filter($filter['difficulties']);
        if (count($difficulties) < 3) {
            $difficulties = array_map(
                fn (string $difficulty) => (int) preg_replace('/\D/', '', $difficulty),
                array_keys($difficulties)
            );
            $recipes->whereIn('difficulty', $difficulties);
        }

        Inertia::share('filter', $filter);
        Inertia::share(
            'filterable',
            array_map('ucfirst', array_keys(
                Arr::where(
                    FilterRequest::defaults(),
                    fn (mixed $value, string $key) =>
                        is_array($value) && !in_array($key, ['ingredients', 'prepTime', 'difficulties'])
                )
            ))
        );

        return $recipes;
    }
}
