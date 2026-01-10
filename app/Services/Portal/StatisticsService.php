<?php

namespace App\Services\Portal;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use stdClass;

class StatisticsService
{
    /**
     * Cache TTL in seconds (1 hour).
     */
    protected int $cacheTtl = 3600;

    /**
     * The cache keys used by the recipe statistics.
     *
     * @var list<string>
     */
    protected array $cacheKeys = [
        'portal_global_stats',
        'portal_country_stats',
        'portal_newest_recipes',
        'portal_difficulty_distribution',
        'portal_recipe_quality',
        'portal_top_ingredients',
        'portal_top_tags',
        'portal_top_cuisines',
        'portal_recipes_per_month',
        'portal_avg_prep_times',
        'portal_data_health',
    ];

    /**
     * Clear all statistics cache.
     */
    public function clearCache(): void
    {
        foreach ($this->cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Warm all recipe statistics cache.
     */
    public function warmCache(): void
    {
        $this->clearCache();

        $this->globalStats();
        $this->countryStats();
        $this->newestRecipes();
        $this->difficultyDistribution();
        $this->recipeQuality();
        $this->topIngredients();
        $this->topTags();
        $this->topCuisines();
        $this->recipesPerMonth();
        $this->avgPrepTimesByCountry();
        $this->dataHealth();
    }

    /**
     * Get global statistics.
     *
     * @return array{recipes: int, ingredients: int, menus: int, countries: int, tags: int, allergens: int, cuisines: int}
     */
    public function globalStats(): array
    {
        /** @var array{recipes: int, ingredients: int, menus: int, countries: int, tags: int, allergens: int, cuisines: int} */
        return Cache::remember('portal_global_stats', $this->cacheTtl, static fn (): array => [
            'recipes' => Recipe::count(),
            'ingredients' => Ingredient::count(),
            'menus' => Menu::count(),
            'countries' => Country::where('active', true)->count(),
            'tags' => Tag::count(),
            'allergens' => Allergen::count(),
            'cuisines' => Cuisine::count(),
        ]);
    }

    /**
     * Get per-country statistics.
     *
     * @return Collection<int, Country>
     */
    public function countryStats(): Collection
    {
        /** @var Collection<int, Country> */
        return Cache::remember('portal_country_stats', $this->cacheTtl, static fn (): Collection => Country::where('active', true)
            ->withCount('menus')
            ->get());
    }

    /**
     * Get the newest recipes.
     *
     * @return Collection<int, Recipe>
     */
    public function newestRecipes(): Collection
    {
        /** @var Collection<int, Recipe> */
        return Cache::remember('portal_newest_recipes', $this->cacheTtl, static fn (): Collection => Recipe::with('country')
            ->latest('created_at')
            ->limit(5)
            ->get());
    }

    /**
     * Get recipe count per difficulty level.
     *
     * @return array<int, array{difficulty: int, count: int}>
     */
    public function difficultyDistribution(): array
    {
        /** @var array<int, array{difficulty: int, count: int}> */
        return Cache::remember('portal_difficulty_distribution', $this->cacheTtl, static function (): array {
            $results = DB::table('recipes')
                ->selectRaw('difficulty, COUNT(*) as count')
                ->whereNotNull('difficulty')
                ->groupBy('difficulty')
                ->orderBy('difficulty')
                ->get();

            return $results->map(static fn (stdClass $row): array => [
                'difficulty' => is_numeric($row->difficulty) ? (int) $row->difficulty : 0,
                'count' => is_numeric($row->count) ? (int) $row->count : 0,
            ])->all();
        });
    }

    /**
     * Get recipe quality statistics.
     *
     * @return array{total: int, without_image: int, without_nutrition: int, with_pdf: int, pdf_percentage: float}
     */
    public function recipeQuality(): array
    {
        /** @var array{total: int, without_image: int, without_nutrition: int, with_pdf: int, pdf_percentage: float} */
        return Cache::remember('portal_recipe_quality', $this->cacheTtl, static function (): array {
            $total = Recipe::count();
            $withoutImage = Recipe::whereNull('image_path')->count();
            $withoutNutrition = Recipe::whereNull('nutrition_primary')->count();
            $withPdf = Recipe::where('has_pdf', true)->count();

            return [
                'total' => $total,
                'without_image' => $withoutImage,
                'without_nutrition' => $withoutNutrition,
                'with_pdf' => $withPdf,
                'pdf_percentage' => $total > 0 ? round(($withPdf / $total) * 100, 1) : 0.0,
            ];
        });
    }

    /**
     * Get top ingredients by recipe count.
     *
     * @return Collection<int, stdClass>
     */
    public function topIngredients(): Collection
    {
        /** @var Collection<int, stdClass> */
        return Cache::remember('portal_top_ingredients', $this->cacheTtl, static fn (): Collection => DB::table('ingredients')
            ->join('ingredient_recipe', 'ingredients.id', '=', 'ingredient_recipe.ingredient_id')
            ->join('countries', 'ingredients.country_id', '=', 'countries.id')
            ->select('ingredients.name', 'countries.code as country_code', 'countries.locales as country_locales', DB::raw('COUNT(ingredient_recipe.recipe_id) as recipes_count'))
            ->groupBy('ingredients.id', 'ingredients.name', 'countries.code', 'countries.locales')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get top tags by recipe count.
     *
     * @return Collection<int, stdClass>
     */
    public function topTags(): Collection
    {
        /** @var Collection<int, stdClass> */
        return Cache::remember('portal_top_tags', $this->cacheTtl, static fn (): Collection => DB::table('tags')
            ->join('recipe_tag', 'tags.id', '=', 'recipe_tag.tag_id')
            ->join('countries', 'tags.country_id', '=', 'countries.id')
            ->select('tags.name', 'countries.code as country_code', 'countries.locales as country_locales', DB::raw('COUNT(recipe_tag.recipe_id) as recipes_count'))
            ->groupBy('tags.id', 'tags.name', 'countries.code', 'countries.locales')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get top cuisines by recipe count.
     *
     * @return Collection<int, stdClass>
     */
    public function topCuisines(): Collection
    {
        /** @var Collection<int, stdClass> */
        return Cache::remember('portal_top_cuisines', $this->cacheTtl, static fn (): Collection => DB::table('cuisines')
            ->join('cuisine_recipe', 'cuisines.id', '=', 'cuisine_recipe.cuisine_id')
            ->join('countries', 'cuisines.country_id', '=', 'countries.id')
            ->select('cuisines.name', 'countries.code as country_code', 'countries.locales as country_locales', DB::raw('COUNT(cuisine_recipe.recipe_id) as recipes_count'))
            ->groupBy('cuisines.id', 'cuisines.name', 'countries.code', 'countries.locales')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get recipes per month for the last 12 months.
     *
     * @return Collection<int, stdClass>
     */
    public function recipesPerMonth(): Collection
    {
        /** @var Collection<int, stdClass> */
        return Cache::remember('portal_recipes_per_month', $this->cacheTtl, static fn (): Collection => DB::table('recipes')
            ->select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->whereNull('deleted_at')
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get());
    }

    /**
     * Get user engagement statistics.
     *
     * Note: User statistics are not cached as they are relatively fast queries
     * and benefit from real-time accuracy.
     *
     * @return array{total_users: int, users_with_lists: int, total_lists: int, total_recipes_in_lists: int}
     */
    public function userEngagement(): array
    {
        $totalUsers = User::count();
        $usersWithLists = User::whereHas('recipeLists')->count();
        $totalLists = RecipeList::count();
        $totalRecipesInLists = DB::table('recipe_recipe_list')->count();

        return [
            'total_users' => $totalUsers,
            'users_with_lists' => $usersWithLists,
            'total_lists' => $totalLists,
            'total_recipes_in_lists' => $totalRecipesInLists,
        ];
    }

    /**
     * Get user counts grouped by country.
     *
     * Note: User statistics are not cached as they are relatively fast queries
     * and benefit from real-time accuracy.
     *
     * @return Collection<int, stdClass>
     */
    public function usersByCountry(): Collection
    {
        return DB::table('users')
            ->select('country_code', DB::raw('COUNT(*) as count'))
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get average prep times per country.
     *
     * @return Collection<int, stdClass>
     */
    public function avgPrepTimesByCountry(): Collection
    {
        /** @var Collection<int, stdClass> */
        return Cache::remember('portal_avg_prep_times', $this->cacheTtl, static fn (): Collection => DB::table('recipes')
            ->join('countries', 'recipes.country_id', '=', 'countries.id')
            ->select(
                'countries.code',
                DB::raw('ROUND(AVG(recipes.prep_time)) as avg_prep'),
                DB::raw('ROUND(AVG(recipes.total_time)) as avg_total')
            )
            ->where('countries.active', true)
            ->whereNull('recipes.deleted_at')
            ->where('recipes.prep_time', '>', 0)
            ->groupBy('countries.id', 'countries.code')
            ->orderBy('countries.code')
            ->get());
    }

    /**
     * Get data health statistics.
     *
     * @return array{orphan_ingredients: int, inactive_countries: int, recipes_without_tags: int}
     */
    public function dataHealth(): array
    {
        /** @var array{orphan_ingredients: int, inactive_countries: int, recipes_without_tags: int} */
        return Cache::remember('portal_data_health', $this->cacheTtl, static function (): array {
            $orphanIngredients = Ingredient::whereDoesntHave('recipes')->count();
            $inactiveCountries = Country::where('active', false)->count();
            $recipesWithoutTags = Recipe::whereDoesntHave('tags')->count();

            return [
                'orphan_ingredients' => $orphanIngredients,
                'inactive_countries' => $inactiveCountries,
                'recipes_without_tags' => $recipesWithoutTags,
            ];
        });
    }
}
