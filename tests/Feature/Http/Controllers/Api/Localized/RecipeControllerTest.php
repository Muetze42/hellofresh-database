<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\Localized;

use App\Models\Country;
use App\Models\Label;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    private string $token;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'DE',
            'locales' => ['de', 'en'],
        ]);

        $user = User::factory()->create();

        // Create a real Sanctum token for API authentication
        $this->token = $user->createToken('test-token')->plainTextToken;
    }

    /**
     * Get the API base URL.
     */
    protected function apiUrl(string $path = ''): string
    {
        $apiDomain = (string) config('api.domain_name');

        return 'http://' . $apiDomain . '/' . ltrim($path, '/');
    }

    /**
     * Make an authenticated API request to the API domain.
     *
     * @param  array<string, mixed>  $headers
     */
    protected function apiGet(string $uri, array $headers = []): TestResponse
    {
        return $this
            ->withToken($this->token)
            ->getJson($this->apiUrl($uri), $headers);
    }

    #[Test]
    public function it_returns_recipes_for_country(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['de' => 'Spaghetti Carbonara'],
        ]);

        $otherCountry = Country::factory()->create(['code' => 'AT']);
        Recipe::factory()->for($otherCountry)->create();

        $response = $this->apiGet('/de-DE/recipes');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $recipe->id);
    }

    #[Test]
    public function it_filters_recipes_by_tag_id(): void
    {
        $tag = Tag::factory()->for($this->country)->create();
        $recipeWithTag = Recipe::factory()->for($this->country)->create();
        $recipeWithTag->tags()->attach($tag);

        Recipe::factory()->for($this->country)->create();

        $response = $this->apiGet('/de-DE/recipes?tag=' . $tag->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $recipeWithTag->id);
    }

    #[Test]
    public function it_filters_recipes_by_label_id(): void
    {
        $label = Label::factory()->for($this->country)->create();
        $recipeWithLabel = Recipe::factory()->for($this->country)->create([
            'label_id' => $label->id,
        ]);

        Recipe::factory()->for($this->country)->create();

        $response = $this->apiGet('/de-DE/recipes?label=' . $label->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $recipeWithLabel->id);
    }

    #[Test]
    public function it_filters_recipes_by_search_term(): void
    {
        // Note: Search uses app locale which is set by ApiLocalizationMiddleware
        // The URL prefix de-DE sets the locale to 'de'
        Recipe::factory()->for($this->country)->create([
            'name' => ['de' => 'Spaghetti Carbonara', 'en' => 'Spaghetti Carbonara'],
        ]);

        Recipe::factory()->for($this->country)->create([
            'name' => ['de' => 'Pizza Margherita', 'en' => 'Pizza Margherita'],
        ]);

        $response = $this->apiGet('/de-DE/recipes?search=Spaghetti');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Spaghetti Carbonara');
    }

    #[Test]
    public function it_filters_recipes_by_difficulty(): void
    {
        $easyRecipe = Recipe::factory()->for($this->country)->create([
            'difficulty' => 1,
        ]);

        Recipe::factory()->for($this->country)->create([
            'difficulty' => 3,
        ]);

        $response = $this->apiGet('/de-DE/recipes?difficulty=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $easyRecipe->id);
    }

    #[Test]
    public function it_filters_recipes_by_has_pdf(): void
    {
        $recipeWithPdf = Recipe::factory()->for($this->country)->withPdf()->create();

        Recipe::factory()->for($this->country)->create([
            'has_pdf' => false,
        ]);

        $response = $this->apiGet('/de-DE/recipes?has_pdf=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $recipeWithPdf->id);
    }

    #[Test]
    public function it_paginates_recipes(): void
    {
        Recipe::factory()->for($this->country)->count(25)->create();

        $response = $this->apiGet('/de-DE/recipes?perPage=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25);
    }

    #[Test]
    public function it_returns_recipe_by_id(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'name' => ['de' => 'Spaghetti Carbonara'],
        ]);

        $response = $this->apiGet('/de-DE/recipes/' . $recipe->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $recipe->id)
            ->assertJsonPath('data.name', 'Spaghetti Carbonara');
    }

    #[Test]
    public function it_returns_404_for_nonexistent_recipe(): void
    {
        $response = $this->apiGet('/de-DE/recipes/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_404_for_recipe_from_other_country(): void
    {
        $otherCountry = Country::factory()->create(['code' => 'AT']);
        $recipe = Recipe::factory()->for($otherCountry)->create();

        $response = $this->apiGet('/de-DE/recipes/' . $recipe->id);

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        // Request without any token to the API domain
        $response = $this->getJson($this->apiUrl('/de-DE/recipes'));

        $response->assertUnauthorized();
    }

    #[Test]
    public function it_requires_verified_email(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $token = $unverifiedUser->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson($this->apiUrl('/de-DE/recipes'));

        $response->assertForbidden();
    }

    #[Test]
    public function it_returns_empty_result_for_invalid_tag_id(): void
    {
        Recipe::factory()->for($this->country)->create();

        $response = $this->apiGet('/de-DE/recipes?tag=99999');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_returns_empty_result_for_invalid_label_id(): void
    {
        Recipe::factory()->for($this->country)->create();

        $response = $this->apiGet('/de-DE/recipes?label=99999');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_combines_multiple_filters(): void
    {
        $tag = Tag::factory()->for($this->country)->create();
        $label = Label::factory()->for($this->country)->create();

        $matchingRecipe = Recipe::factory()->for($this->country)->create([
            'difficulty' => 2,
            'label_id' => $label->id,
        ]);
        $matchingRecipe->tags()->attach($tag);

        // Recipe with tag but wrong difficulty
        $wrongDifficulty = Recipe::factory()->for($this->country)->create([
            'difficulty' => 1,
            'label_id' => $label->id,
        ]);
        $wrongDifficulty->tags()->attach($tag);

        // Recipe with right difficulty but no tag
        Recipe::factory()->for($this->country)->create([
            'difficulty' => 2,
            'label_id' => $label->id,
        ]);

        $response = $this->apiGet(sprintf('/de-DE/recipes?tag=%s&label=%s&difficulty=2', $tag->id, $label->id));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingRecipe->id);
    }

    #[Test]
    public function it_returns_validation_error_for_non_integer_tag(): void
    {
        $response = $this->apiGet('/de-DE/recipes?tag=seasonal');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['tag']);
    }

    #[Test]
    public function it_returns_validation_error_for_non_integer_label(): void
    {
        $response = $this->apiGet('/de-DE/recipes?label=premium');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_difficulty(): void
    {
        $response = $this->apiGet('/de-DE/recipes?difficulty=99');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['difficulty']);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_per_page(): void
    {
        $response = $this->apiGet('/de-DE/recipes?perPage=999');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['perPage']);
    }
}
