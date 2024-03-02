<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ShoppingList/Index');
    }

    /**
     * Get needed data.
     */
    public function data(Request $request): array
    {
        $request->validate([
            'recipes' => 'required|array',
            'form' => 'required|array',
        ]);

        $recipes = Recipe::whereIn('id', $request->input('recipes'))->get(['id', 'name', 'yields']);

        $ingredientIds = $recipes
            ->pluck('yields')
            ->flatten(1)
            ->pluck('ingredients')
            ->flatten(1)
            ->pluck('id')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $ingredient = Ingredient::whereIn('id', $ingredientIds)->get(['id', 'name', 'image_path']);

        return [
            'form' => '', // Todo: Create default form and merge with requested form (required for yield select)
            'recipes' => '', // Todo: Create recipes array<id, string:name>
            'ingredients' => '', // Todo: Create ingredients collection include recipe yields and ids
        ];
    }
}
