<?php

namespace App\Http\Controllers;

use App\Http\Clients\HelloFreshClient;
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
        $client = new HelloFreshClient();

        $item = $client->recipe('63a45de4b228a3cd7316fc34');

        dd($item);

        $recipe = Recipe::updateOrCreate(
            ['id' => $item->getKey()],
            Recipe::freshAttributes($item)
        );

        dd($recipe);

        return Inertia::render('Recipes/Index', [
            'recipes' => RecipeResource::indexCollection(
                Recipe::paginate(12, ['*'], 'p')
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
