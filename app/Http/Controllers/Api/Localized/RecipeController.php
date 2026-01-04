<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\RecipeCollection;
use App\Http\Resources\Api\RecipeResource;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Throwable;

class RecipeController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     *
     * @throws Throwable
     */
    public function index(Request $request): RecipeCollection
    {
        return new RecipeCollection(
            Recipe::where('country_id', $this->country()->id)
                ->with(['label', 'tags'])
                ->when($request->filled('search'), function (Builder $query) use ($request): void {
                    $searchTerm = '%' . $request->string('search') . '%';
                    $locale = app()->getLocale();

                    $query->where(function (Builder $query) use ($searchTerm, $locale): void {
                        $query->whereLike('name->' . $locale, $searchTerm)
                            ->orWhereLike('headline->' . $locale, $searchTerm);
                    });
                })
                ->when($request->integer('tag') > 0, function (Builder $query) use ($request): void {
                    $query->whereHas('tags', function (Builder $query) use ($request): void {
                        $query->where('id', $request->integer('tag'));
                    });
                })
                ->when($request->integer('label') > 0, function (Builder $query) use ($request): void {
                    $query->whereHas('label', function (Builder $query) use ($request): void {
                        $query->where('id', $request->integer('label'));
                    });
                })
                ->when($request->filled('difficulty'), function (Builder $query) use ($request): void {
                    $query->where('difficulty', $request->input('difficulty'));
                })
                ->when($request->boolean('has_pdf'), function (Builder $query): void {
                    $query->where('has_pdf', true);
                })
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $recipeId): RecipeResource
    {
        $country = $this->country();

        $recipe = Recipe::where('country_id', $country->id)
            ->where('id', $recipeId)
            ->with(['country', 'label', 'tags', 'allergens', 'ingredients', 'cuisines', 'utensils'])
            ->firstOrFail();

        return new RecipeResource($recipe);
    }
}
