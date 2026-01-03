<?php

namespace App\Livewire\Portal;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use stdClass;

#[Layout('portal::components.layouts.app')]
class Statistic extends Component
{
    public string $sortBy = 'recipes_count';

    public string $sortDirection = 'desc';

    /**
     * Sort the country stats by a given column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }

    /**
     * Get global statistics.
     *
     * @return array{recipes: int, ingredients: int, menus: int, countries: int, tags: int, allergens: int, cuisines: int}
     */
    #[Computed]
    public function globalStats(): array
    {
        return Cache::remember('portal_global_stats', 3600, static fn (): array => [
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
    #[Computed]
    public function countryStats(): Collection
    {
        $countries = Cache::remember('portal_country_stats', 3600, static fn (): Collection => Country::where('active', true)
            ->withCount('menus')
            ->get());

        return $this->sortDirection === 'asc'
            ? $countries->sortBy($this->sortBy)
            : $countries->sortByDesc($this->sortBy);
    }

    /**
     * Get the newest recipes.
     *
     * @return Collection<int, Recipe>
     */
    #[Computed]
    public function newestRecipes(): Collection
    {
        return Cache::remember('portal_newest_recipes', 3600, static fn (): Collection => Recipe::with('country')
            ->latest('created_at')
            ->limit(5)
            ->get());
    }

    /**
     * Get recipe count per difficulty level.
     *
     * @return array<int, array{difficulty: int, count: int}>
     */
    #[Computed]
    public function difficultyDistribution(): array
    {
        return Cache::remember('portal_difficulty_distribution', 3600, static function (): array {
            $results = DB::table('recipes')
                ->selectRaw('difficulty, COUNT(*) as count')
                ->whereNotNull('difficulty')
                ->groupBy('difficulty')
                ->orderBy('difficulty')
                ->get();

            return $results->map(fn (stdClass $row): array => [
                'difficulty' => (int) $row->difficulty,
                'count' => (int) $row->count,
            ])->all();
        });
    }

    /**
     * Get recipe quality statistics.
     *
     * @return array{total: int, without_image: int, without_nutrition: int, with_pdf: int, pdf_percentage: float}
     */
    #[Computed]
    public function recipeQuality(): array
    {
        return Cache::remember('portal_recipe_quality', 3600, static function (): array {
            $total = Recipe::count();
            $withoutImage = Recipe::whereNull('image_path')->count();
            $withoutNutrition = Recipe::whereNull('nutrition_primary')->count();
            $withPdf = Recipe::where('has_pdf', true)->count();

            return [
                'total' => $total,
                'without_image' => $withoutImage,
                'without_nutrition' => $withoutNutrition,
                'with_pdf' => $withPdf,
                'pdf_percentage' => $total > 0 ? round(($withPdf / $total) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Get top ingredients by recipe count.
     *
     * @return Collection<int, object{name: string, recipes_count: int}>
     */
    #[Computed]
    public function topIngredients(): Collection
    {
        return Cache::remember('portal_top_ingredients', 3600, static fn (): Collection => DB::table('ingredients')
            ->join('ingredient_recipe', 'ingredients.id', '=', 'ingredient_recipe.ingredient_id')
            ->select('ingredients.name', DB::raw('COUNT(ingredient_recipe.recipe_id) as recipes_count'))
            ->groupBy('ingredients.id', 'ingredients.name')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get top tags by recipe count.
     *
     * @return Collection<int, object{name: string, recipes_count: int}>
     */
    #[Computed]
    public function topTags(): Collection
    {
        return Cache::remember('portal_top_tags', 3600, static fn (): Collection => DB::table('tags')
            ->join('recipe_tag', 'tags.id', '=', 'recipe_tag.tag_id')
            ->select('tags.name', DB::raw('COUNT(recipe_tag.recipe_id) as recipes_count'))
            ->groupBy('tags.id', 'tags.name')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get top cuisines by recipe count.
     *
     * @return Collection<int, object{name: string, recipes_count: int}>
     */
    #[Computed]
    public function topCuisines(): Collection
    {
        return Cache::remember('portal_top_cuisines', 3600, static fn (): Collection => DB::table('cuisines')
            ->join('cuisine_recipe', 'cuisines.id', '=', 'cuisine_recipe.cuisine_id')
            ->select('cuisines.name', DB::raw('COUNT(cuisine_recipe.recipe_id) as recipes_count'))
            ->groupBy('cuisines.id', 'cuisines.name')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get());
    }

    /**
     * Get recipes per month for the last 12 months.
     *
     * @return Collection<int, object{month: string, count: int}>
     */
    #[Computed]
    public function recipesPerMonth(): Collection
    {
        return Cache::remember('portal_recipes_per_month', 3600, static fn (): Collection => DB::table('recipes')
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
     * @return array{total_users: int, users_with_lists: int, total_lists: int, total_recipes_in_lists: int}
     */
    #[Computed]
    public function userEngagement(): array
    {
        return Cache::remember('portal_user_engagement', 3600, static function (): array {
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
        });
    }

    /**
     * Get average prep times per country.
     *
     * @return Collection<int, object{code: string, avg_prep: float, avg_total: float}>
     */
    #[Computed]
    public function avgPrepTimesByCountry(): Collection
    {
        return Cache::remember('portal_avg_prep_times', 3600, static fn (): Collection => DB::table('recipes')
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
    #[Computed]
    public function dataHealth(): array
    {
        return Cache::remember('portal_data_health', 3600, static function (): array {
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

    public function render(): View
    {
        return view('portal::livewire.statistic.index')->title('Database Statistics');
    }
}
