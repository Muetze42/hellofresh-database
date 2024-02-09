<?php

namespace App\Http\Controllers;

use App\Http\Resources\PageIndices\RecipeResource;
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
            'recipes' => RecipeResource::collection(
                country()->recipes()->paginate(12, ['*'], 'p')
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
