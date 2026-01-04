<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Portal;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\Tag;
use App\Models\User;
use App\Services\Portal\StatisticsService;
use Illuminate\Support\Facades\Cache;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class StatisticsServiceTest extends TestCase
{
    private StatisticsService $statisticsService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->statisticsService = new StatisticsService();
        Cache::flush();
    }

    #[Test]
    public function it_clears_all_cache_keys(): void
    {
        Cache::put('portal_global_stats', ['test' => 1], 3600);
        Cache::put('portal_country_stats', ['test' => 2], 3600);

        $this->statisticsService->clearCache();

        $this->assertFalse(Cache::has('portal_global_stats'));
        $this->assertFalse(Cache::has('portal_country_stats'));
    }

    #[Test]
    public function it_warms_all_cache_keys(): void
    {
        Country::factory()->create(['active' => true]);
        Recipe::factory()->create();

        $this->statisticsService->warmCache();

        $this->assertTrue(Cache::has('portal_global_stats'));
        $this->assertTrue(Cache::has('portal_country_stats'));
        $this->assertTrue(Cache::has('portal_newest_recipes'));
        $this->assertTrue(Cache::has('portal_difficulty_distribution'));
        $this->assertTrue(Cache::has('portal_recipe_quality'));
        $this->assertTrue(Cache::has('portal_top_ingredients'));
        $this->assertTrue(Cache::has('portal_top_tags'));
        $this->assertTrue(Cache::has('portal_top_cuisines'));
        $this->assertTrue(Cache::has('portal_recipes_per_month'));
        $this->assertTrue(Cache::has('portal_avg_prep_times'));
        $this->assertTrue(Cache::has('portal_data_health'));
    }

    #[Test]
    public function it_returns_global_stats_with_correct_structure(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->count(3)->create();
        Ingredient::factory()->for($country)->count(5)->create();
        Menu::factory()->for($country)->create();
        Tag::factory()->for($country)->count(2)->create();
        Allergen::factory()->for($country)->count(4)->create();
        Cuisine::factory()->for($country)->create();

        $result = $this->statisticsService->globalStats();

        $this->assertArrayHasKey('recipes', $result);
        $this->assertArrayHasKey('ingredients', $result);
        $this->assertArrayHasKey('menus', $result);
        $this->assertArrayHasKey('countries', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('allergens', $result);
        $this->assertArrayHasKey('cuisines', $result);
        $this->assertSame(3, $result['recipes']);
        $this->assertSame(5, $result['ingredients']);
        $this->assertSame(1, $result['menus']);
        $this->assertSame(1, $result['countries']);
    }

    #[Test]
    public function it_caches_global_stats(): void
    {
        Country::factory()->create(['active' => true]);

        $this->statisticsService->globalStats();

        $this->assertTrue(Cache::has('portal_global_stats'));
    }

    #[Test]
    public function it_returns_country_stats_with_menu_counts(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Menu::factory()->for($country)->count(3)->create();
        Country::factory()->create(['active' => false]);

        $result = $this->statisticsService->countryStats();

        $this->assertCount(1, $result);
        $this->assertSame(3, $result->first()->menus_count);
    }

    #[Test]
    public function it_returns_newest_recipes(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->create(['created_at' => now()->subDays(10)]);
        $newRecipe = Recipe::factory()->for($country)->create(['created_at' => now()]);

        $result = $this->statisticsService->newestRecipes();

        $this->assertCount(2, $result);
        $this->assertSame($newRecipe->id, $result->first()->id);
        $this->assertTrue($result->first()->relationLoaded('country'));
    }

    #[Test]
    public function it_returns_at_most_five_newest_recipes(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->count(10)->create();

        $result = $this->statisticsService->newestRecipes();

        $this->assertCount(5, $result);
    }

    #[Test]
    public function it_returns_difficulty_distribution(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->create(['difficulty' => 1]);
        Recipe::factory()->for($country)->create(['difficulty' => 1]);
        Recipe::factory()->for($country)->create(['difficulty' => 2]);
        Recipe::factory()->for($country)->create(['difficulty' => 3]);

        $result = $this->statisticsService->difficultyDistribution();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('difficulty', $result[0]);
        $this->assertArrayHasKey('count', $result[0]);
    }

    #[Test]
    public function it_excludes_null_difficulty_from_distribution(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->create(['difficulty' => null]);
        Recipe::factory()->for($country)->create(['difficulty' => 1]);

        $result = $this->statisticsService->difficultyDistribution();

        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['difficulty']);
    }

    #[Test]
    public function it_returns_recipe_quality_statistics(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->withPdf()->count(3)->create([
            'image_path' => 'some/path.jpg',
            'nutrition_primary' => ['calories' => 100],
        ]);
        Recipe::factory()->for($country)->create([
            'image_path' => null,
            'nutrition_primary' => null,
        ]);

        $result = $this->statisticsService->recipeQuality();

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('without_image', $result);
        $this->assertArrayHasKey('without_nutrition', $result);
        $this->assertArrayHasKey('with_pdf', $result);
        $this->assertArrayHasKey('pdf_percentage', $result);
        $this->assertSame(4, $result['total']);
        $this->assertSame(1, $result['without_image']);
        $this->assertSame(1, $result['without_nutrition']);
        $this->assertSame(3, $result['with_pdf']);
    }

    #[Test]
    public function it_calculates_pdf_percentage_correctly(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->withPdf()->count(3)->create();
        Recipe::factory()->for($country)->count(7)->create();

        $result = $this->statisticsService->recipeQuality();

        $this->assertEqualsWithDelta(30.0, $result['pdf_percentage'], PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function it_handles_zero_recipes_for_pdf_percentage(): void
    {
        $result = $this->statisticsService->recipeQuality();

        $this->assertSame(0, $result['total']);
        $this->assertEqualsWithDelta(0.0, $result['pdf_percentage'], PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function it_returns_top_ingredients(): void
    {
        $country = Country::factory()->create(['active' => true]);
        $ingredient1 = Ingredient::factory()->for($country)->create();
        $ingredient2 = Ingredient::factory()->for($country)->create();
        $recipe1 = Recipe::factory()->for($country)->create();
        $recipe2 = Recipe::factory()->for($country)->create();
        $recipe3 = Recipe::factory()->for($country)->create();
        $recipe1->ingredients()->attach([$ingredient1->id, $ingredient2->id]);
        $recipe2->ingredients()->attach($ingredient1->id);
        $recipe3->ingredients()->attach($ingredient1->id);

        $result = $this->statisticsService->topIngredients();

        $this->assertGreaterThanOrEqual(1, $result->count());
        $this->assertSame(3, (int) $result->first()->recipes_count);
    }

    #[Test]
    public function it_returns_at_most_ten_top_ingredients(): void
    {
        $country = Country::factory()->create(['active' => true]);
        $ingredients = Ingredient::factory()->for($country)->count(15)->create();
        $recipe = Recipe::factory()->for($country)->create();
        $recipe->ingredients()->attach($ingredients->pluck('id'));

        $result = $this->statisticsService->topIngredients();

        $this->assertCount(10, $result);
    }

    #[Test]
    public function it_returns_top_tags(): void
    {
        $country = Country::factory()->create(['active' => true]);
        $tag = Tag::factory()->for($country)->create();
        $recipes = Recipe::factory()->for($country)->count(3)->create();
        foreach ($recipes as $recipe) {
            $recipe->tags()->attach($tag->id);
        }

        $result = $this->statisticsService->topTags();

        $this->assertGreaterThanOrEqual(1, $result->count());
        $this->assertSame(3, (int) $result->first()->recipes_count);
    }

    #[Test]
    public function it_returns_top_cuisines(): void
    {
        $country = Country::factory()->create(['active' => true]);
        $cuisine = Cuisine::factory()->for($country)->create();
        $recipes = Recipe::factory()->for($country)->count(4)->create();
        foreach ($recipes as $recipe) {
            $recipe->cuisines()->attach($cuisine->id);
        }

        $result = $this->statisticsService->topCuisines();

        $this->assertGreaterThanOrEqual(1, $result->count());
        $this->assertSame(4, (int) $result->first()->recipes_count);
    }

    #[Test]
    public function it_returns_recipes_per_month(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->create(['created_at' => now()]);
        Recipe::factory()->for($country)->create(['created_at' => now()->subMonth()]);
        Recipe::factory()->for($country)->create(['created_at' => now()->subMonths(2)]);

        $result = $this->statisticsService->recipesPerMonth();

        $this->assertGreaterThanOrEqual(1, $result->count());
    }

    #[Test]
    public function it_excludes_recipes_older_than_12_months(): void
    {
        $country = Country::factory()->create(['active' => true]);
        Recipe::factory()->for($country)->create(['created_at' => now()->subMonths(13)]);

        $result = $this->statisticsService->recipesPerMonth();

        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_user_engagement_statistics(): void
    {
        $country = Country::factory()->create(['active' => true]);
        $user1 = User::factory()->create();
        User::factory()->create();
        User::factory()->create();
        $list = RecipeList::factory()->for($user1)->create();
        $recipe = Recipe::factory()->for($country)->create();
        $list->recipes()->attach($recipe->id, ['country_id' => $country->id]);

        $result = $this->statisticsService->userEngagement();

        $this->assertArrayHasKey('total_users', $result);
        $this->assertArrayHasKey('users_with_lists', $result);
        $this->assertArrayHasKey('total_lists', $result);
        $this->assertArrayHasKey('total_recipes_in_lists', $result);
        $this->assertSame(3, $result['total_users']);
        $this->assertSame(1, $result['users_with_lists']);
        $this->assertSame(1, $result['total_lists']);
        $this->assertSame(1, $result['total_recipes_in_lists']);
    }

    #[Test]
    public function it_returns_users_by_country(): void
    {
        User::factory()->create(['country_code' => 'DE']);
        User::factory()->create(['country_code' => 'DE']);
        User::factory()->create(['country_code' => 'US']);

        $result = $this->statisticsService->usersByCountry();

        $this->assertGreaterThanOrEqual(2, $result->count());
        $this->assertSame('DE', $result->first()->country_code);
        $this->assertSame(2, (int) $result->first()->count);
    }

    #[Test]
    public function it_returns_avg_prep_times_by_country(): void
    {
        $country = Country::factory()->create(['active' => true, 'code' => 'DE']);
        Recipe::factory()->for($country)->create(['prep_time' => 10, 'total_time' => 30]);
        Recipe::factory()->for($country)->create(['prep_time' => 20, 'total_time' => 50]);

        $result = $this->statisticsService->avgPrepTimesByCountry();

        $this->assertGreaterThanOrEqual(1, $result->count());
        $this->assertSame('DE', $result->first()->code);
        $this->assertSame(15, (int) $result->first()->avg_prep);
        $this->assertSame(40, (int) $result->first()->avg_total);
    }

    #[Test]
    public function it_excludes_inactive_countries_from_avg_prep_times(): void
    {
        $activeCountry = Country::factory()->create(['active' => true, 'code' => 'DE']);
        $inactiveCountry = Country::factory()->create(['active' => false, 'code' => 'FR']);
        Recipe::factory()->for($activeCountry)->create(['prep_time' => 10, 'total_time' => 30]);
        Recipe::factory()->for($inactiveCountry)->create(['prep_time' => 20, 'total_time' => 50]);

        $result = $this->statisticsService->avgPrepTimesByCountry();

        $this->assertCount(1, $result);
        $this->assertSame('DE', $result->first()->code);
    }

    #[Test]
    public function it_excludes_zero_prep_time_from_averages(): void
    {
        $country = Country::factory()->create(['active' => true, 'code' => 'DE']);
        Recipe::factory()->for($country)->create(['prep_time' => 0, 'total_time' => 30]);
        Recipe::factory()->for($country)->create(['prep_time' => 20, 'total_time' => 50]);

        $result = $this->statisticsService->avgPrepTimesByCountry();

        $this->assertSame(20, (int) $result->first()->avg_prep);
    }

    #[Test]
    public function it_returns_data_health_statistics(): void
    {
        $activeCountry = Country::factory()->create(['active' => true]);
        Country::factory()->create(['active' => false]);
        $tag = Tag::factory()->for($activeCountry)->create();
        Ingredient::factory()->for($activeCountry)->create();
        $recipeWithTag = Recipe::factory()->for($activeCountry)->create();
        $recipeWithTag->tags()->attach($tag->id);
        Recipe::factory()->for($activeCountry)->create();

        $result = $this->statisticsService->dataHealth();

        $this->assertArrayHasKey('orphan_ingredients', $result);
        $this->assertArrayHasKey('inactive_countries', $result);
        $this->assertArrayHasKey('recipes_without_tags', $result);
        $this->assertSame(1, $result['orphan_ingredients']);
        $this->assertSame(1, $result['inactive_countries']);
        $this->assertSame(1, $result['recipes_without_tags']);
    }

    #[Test]
    public function it_caches_data_health_statistics(): void
    {
        $this->statisticsService->dataHealth();

        $this->assertTrue(Cache::has('portal_data_health'));
    }

    #[Test]
    public function it_does_not_cache_user_engagement(): void
    {
        $this->statisticsService->userEngagement();

        $this->assertFalse(Cache::has('portal_user_engagement'));
    }

    #[Test]
    public function it_does_not_cache_users_by_country(): void
    {
        $this->statisticsService->usersByCountry();

        $this->assertFalse(Cache::has('portal_users_by_country'));
    }
}
