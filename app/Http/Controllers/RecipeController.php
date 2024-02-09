<?php

namespace App\Http\Controllers;

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
        dd(__CLASS__);
        /* @var \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe $recipes */
        $recipes = country()->recipes();

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes->paginate(12, ['*'], 'p')
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
