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
        $recipes = RecipeResource::indexCollection(
            $this->recipesFilterQuery(Recipe::query(), $request)
                ->orderBy('external_updated_at')
                ->paginate(config('application.pagination.per_page', 12))
                ->withQueryString()
        );

        return Inertia::render('Recipes/Index', ['recipes' => $recipes])
            ->toResponse($request)
            ->setStatusCode($recipes->count() ? 200 : 404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        dd($recipe);
    }
}
