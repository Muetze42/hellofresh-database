<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //dd(iso8601ToMinutes('PT1H25M'));
        //dd(\Carbon\CarbonInterval::make('PT1H25M')->totalMinutes);
        //dd((new \DateInterval('PT1H25M')));
        dd(Recipe::whereNotNull('prep_time')->pluck('prep_time')->unique()->toArray());

        return Inertia::render('Recipes/Index', [
            'recipes' => RecipeResource::indexCollection(
                $this->recipesFilterQuery(Recipe::query(), $request)
                    ->orderBy('external_updated_at')
                    ->paginate(config('application.pagination.per_page', 12))
                    ->withQueryString()
            ),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        dd($recipe);
    }
}
