<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'form' => 'array',
        ]);

        $form = $request->input('form', []);

        $recipes = Recipe::whereIn('id', $request->input('recipes'))
            ->get(['id', 'name', 'yields', 'image_path']);

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

        $recipes = $recipes->map(fn (Recipe $recipe) => [
            'id' => $recipe->getKey(),
            'name' => $recipe->name,
            'yields' => (array) $recipe->yields,
            'image' => $recipe->asset()->preview(),
            'options' => array_map(fn (int $yields) => $yields . 'p', Arr::pluck((array) $recipe->yields, 'yields')),
        ])->toArray();

        $ingredients = Ingredient::whereIn('id', $ingredientIds)
            ->get()
            ->mapWithKeys(fn (Ingredient $ingredient) =>
                [$ingredient->getKey() => [
                    'name' => $ingredient->name,
                    'image' => $ingredient->asset()->image(),
                    'recipe_yields' => [],
                ]])->toArray();

        foreach ($recipes as $recipe) {
            if (!count($recipe['options'])) {
                continue;
            }
            $form[$recipe['id']] = !empty($form[$recipe['id']]) && in_array($form[$recipe['id']], $recipe['options']) ?
                $form[$recipe['id']] : $recipe['yields'][array_key_first($recipe['yields'])]['yields'];

            foreach ($recipe['yields'] as $yield) {
                $yields = $yield['yields'];
                foreach ($yield['ingredients'] as $ingredient) {
                    $ingredients[$ingredient['id']]['recipe_yields'][$recipe['id']][$yields . 'p'] = [
                        'amount' => $ingredient['amount'],
                        'unit' => $ingredient['unit'],
                    ];
                }
            }
        }

        return [
            'form' => $form,
            'recipes' => Arr::mapWithKeys($recipes, fn ($recipe) => [$recipe['id'] => [
                'name' => $recipe['name'],
                'image' => $recipe['image'],
            ]]),
            'ingredients' => $ingredients,
        ];
    }
}
