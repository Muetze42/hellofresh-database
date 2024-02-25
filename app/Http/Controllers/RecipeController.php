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
        return Inertia::render('Recipes/Index', [
            'recipes' => RecipeResource::indexCollection(
                $this->recipesFilterQuery(Recipe::query(), $request)
                    ->orderBy('external_updated_at')
                    ->paginate(config('application.pagination.per_page', 12))
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
