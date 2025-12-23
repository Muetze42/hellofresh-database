<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Recipe;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BringExportController extends Controller
{
    /**
     * Display the shopping list in Schema.org format for Bring import.
     */
    public function __invoke(Request $request, Country $country): View
    {
        $recipeIds = $this->parseRecipeIds($request->string('recipes')->toString());
        $servingsMap = $this->parseServings($request->string('servings')->toString());

        $recipes = Recipe::whereIn('id', $recipeIds)
            ->where('country_id', $country->id)
            ->with('ingredients')
            ->get();

        $ingredients = $this->aggregateIngredients($recipes, $servingsMap);

        return view('bring-export', [
            'ingredients' => $ingredients,
            'recipes' => $recipes,
        ]);
    }

    /**
     * Parse comma-separated recipe IDs.
     *
     * @return list<int>
     */
    protected function parseRecipeIds(string $recipes): array
    {
        if ($recipes === '') {
            return [];
        }

        return array_map(intval(...), explode(',', $recipes));
    }

    /**
     * Parse servings string (format: "recipeId:servings,recipeId:servings").
     *
     * @return array<int, int>
     */
    protected function parseServings(string $servings): array
    {
        if ($servings === '') {
            return [];
        }

        $map = [];

        foreach (explode(',', $servings) as $pair) {
            $parts = explode(':', $pair);
            if (count($parts) === 2) {
                $map[(int) $parts[0]] = (int) $parts[1];
            }
        }

        return $map;
    }

    /**
     * Aggregate ingredients from all recipes with their quantities.
     *
     * @param  Collection<int, Recipe>  $recipes
     * @param  array<int, int>  $servingsMap
     * @return list<array{name: string, amount: string}>
     */
    protected function aggregateIngredients(Collection $recipes, array $servingsMap): array
    {
        $aggregated = [];

        foreach ($recipes as $recipe) {
            $servings = $servingsMap[$recipe->id] ?? 2;
            $yieldsData = $this->getYieldsDataForServings($recipe, $servings);

            foreach ($recipe->ingredients as $ingredient) {
                $ingredientData = $this->findIngredientInYields($yieldsData, $ingredient->getTranslation('name', app()->getLocale()));

                $name = $ingredient->name;
                $amount = $ingredientData['amount'];
                $unit = $ingredientData['unit'];

                $key = $name;

                if (! isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'name' => $name,
                        'amounts' => [],
                    ];
                }

                if ($amount !== null && $unit !== '') {
                    $aggregated[$key]['amounts'][] = [
                        'amount' => $amount,
                        'unit' => $unit,
                    ];
                }
            }
        }

        // Format for output
        return array_values(array_map(function (array $item): array {
            $amountStr = '';

            if ($item['amounts'] !== []) {
                // Group by unit and sum
                $byUnit = [];
                foreach ($item['amounts'] as $amt) {
                    $unit = $amt['unit'];
                    $byUnit[$unit] = ($byUnit[$unit] ?? 0) + $amt['amount'];
                }

                $parts = [];
                foreach ($byUnit as $unit => $total) {
                    $parts[] = number_format($total, $total == (int) $total ? 0 : 1) . ' ' . $unit;
                }

                $amountStr = implode(', ', $parts);
            }

            return [
                'name' => $item['name'],
                'amount' => $amountStr,
            ];
        }, $aggregated));
    }

    /**
     * Get yields data for specific servings.
     *
     * @return array<string, array{amount: float|null, unit: string}>
     */
    protected function getYieldsDataForServings(Recipe $recipe, int $servings): array
    {
        /** @var list<array<string, mixed>>|null $yields */
        $yields = $recipe->yields_primary ?? $recipe->yields_secondary;

        if (! is_array($yields)) {
            return [];
        }

        foreach ($yields as $yield) {
            if (($yield['yields'] ?? 0) === $servings) {
                $result = [];
                /** @var list<array<string, mixed>> $ingredients */
                $ingredients = $yield['ingredients'] ?? [];
                foreach ($ingredients as $ingredient) {
                    $name = (string) ($ingredient['name'] ?? '');
                    $result[$name] = [
                        'amount' => isset($ingredient['amount']) ? (float) $ingredient['amount'] : null,
                        'unit' => (string) ($ingredient['unit'] ?? ''),
                    ];
                }

                return $result;
            }
        }

        return [];
    }

    /**
     * Find ingredient data in yields.
     *
     * @param  array<string, array{amount: float|null, unit: string}>  $yieldsData
     * @return array{amount: float|null, unit: string}
     */
    protected function findIngredientInYields(array $yieldsData, string $ingredientName): array
    {
        return $yieldsData[$ingredientName] ?? ['amount' => null, 'unit' => ''];
    }
}
