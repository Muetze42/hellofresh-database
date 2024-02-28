<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecipeResource;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Menu;
use App\Models\Recipe;
use App\Support\Requests\FilterRequest;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RecipeMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ?Menu $menu = null)
    {
        $recipes = $this->filterQuery($request)
            ->when(
                $menu,
                fn (Builder $query) => $query->whereIn('id', $menu->recipes->pluck('id')->toArray())
            )
            ->orderBy('external_updated_at')
            ->paginate(config('application.pagination.per_page', 12))
            ->withQueryString();

        return Inertia::render('Recipes', [
            'recipes' => RecipeResource::indexCollection($recipes),
            'menus' => $this->menuData($menu),
        ])->toResponse($request)->setStatusCode($recipes->count() ? 200 : 404);
    }

    /**
     * Get the menu data for current the request.
     */
    protected function menuData(?Menu $menu): ?array
    {
        if (!$menu) {
            return null;
        }

        $formatted = $this->formattedCurrentMenuWeek();

        return [
            'current' => [
                'value' => $menu->year_week,
                'start' => $menu->start->startOfWeek(CarbonInterface::SATURDAY)->publicFormatted(),
                'end' => $menu->start->endOfWeek(CarbonInterface::FRIDAY)->publicFormatted(),
            ],
            'list' => Menu::where('year_week', '>=', $formatted)
                //->whereNot('year_week', $menu->year_week)
                ->get()
                ->map(fn (Menu $menu) => [
                    'value' => $menu->year_week,
                    'start' => $menu->start->startOfWeek(CarbonInterface::SATURDAY)->publicFormatted(),
                    'end' => $menu->start->endOfWeek(CarbonInterface::FRIDAY)->publicFormatted(),
                ])->toArray(),
        ];
    }

    /**
     * Get the current menu week formatted for a query.
     */
    protected function formattedCurrentMenuWeek()
    {
        $week = now()->subWeek();

        return $week->format('YW');
    }

    /**
     * Find the current menu and redirect to it.
     */
    public function findMenu()
    {
        $formatted = $this->formattedCurrentMenuWeek();

        $menu = Menu::where('year_week', '>=', $formatted)->first();

        if ($menu) {
            return redirect(countryRoute('recipes.menus', ['menu' => $menu->year_week]));
        }

        if ($menu = Menu::orderByDesc('year_week')->first()) {
            /* @var \App\Models\Menu $menu */
            return redirect(countryRoute('recipes.menus', ['menu' => $menu->year_week]));
        }

        abort(404);
    }

    /**
     * Build a filtered query for recipes.
     */
    protected function filterQuery(Request $request): Builder
    {
        $recipes = Recipe::active();
        $filter = FilterRequest::parse($request);
        $filterable = FilterRequest::filterable();

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

        if (!empty($filter['labels'])) {
            $recipes->whereIn('label_id', $filter['labels']);
            $filter['labels'] = Label::whereIn('id', $filter['labels'])
                ->get(['text', 'id'])
                ->map(fn (Label $label) => ['id' => $label->id, 'name' => $label->text]);
        }

        if (!empty($filter['labels_except'])) {
            $recipes->whereNotIn('label_id', $filter['labels_except']);
            $filter['labels_except'] = Label::whereIn('id', $filter['labels_except'])
                ->get(['text', 'id'])
                ->map(fn (Label $label) => ['id' => $label->id, 'name' => $label->text]);
        }

        foreach (
            Arr::only($filter, Arr::where(
                $filterable,
                fn (string $value) => !in_array($value, ['ingredients', 'labels', 'labels_except'])
            )) as $key => $value
        ) {
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
            array_map('ucfirst', Arr::where($filterable, fn (string $value) => $value != 'ingredients'))
        );

        return $recipes;
    }
}
