<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Jobs\Recipe\ImportRecipeJob;
use App\Models\Country;
use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Tests\TestCase;

final class ImportRecipeJobTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    /**
     * @var array<string, mixed>
     */
    private array $validRecipeData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create(['locales' => ['en', 'de']]);
        $this->validRecipeData = $this->createValidRecipeData();
    }

    /**
     * @return array<string, mixed>
     */
    protected function createValidRecipeData(): array
    {
        return [
            'id' => 'hf-recipe-123',
            'name' => 'Test Recipe',
            'headline' => 'A delicious test recipe',
            'descriptionMarkdown' => 'This is a test description.',
            'cardLink' => 'https://example.com/card.pdf',
            'difficulty' => 2,
            'prepTime' => 'PT15M',
            'totalTime' => 'PT30M',
            'imagePath' => 'images/recipe.jpg',
            'active' => true,
            'isAddon' => false,
            'isPublished' => true,
            'steps' => [
                ['index' => 1, 'instructionsMarkdown' => 'Step 1', 'images' => []],
                ['index' => 2, 'instructionsMarkdown' => 'Step 2', 'images' => []],
            ],
            'nutrition' => [
                ['name' => 'Calories', 'amount' => 500, 'unit' => 'kcal'],
            ],
            'yields' => [
                ['yields' => 2, 'ingredients' => []],
                ['yields' => 3, 'ingredients' => []],
                ['yields' => 4, 'ingredients' => []],
            ],
            'ingredients' => [
                ['id' => 'ing-1', 'name' => 'Ingredient 1', 'imagePath' => null],
                ['id' => 'ing-2', 'name' => 'Ingredient 2', 'imagePath' => null],
                ['id' => 'ing-3', 'name' => 'Ingredient 3', 'imagePath' => null],
                ['id' => 'ing-4', 'name' => 'Ingredient 4', 'imagePath' => null],
            ],
            'allergens' => [],
            'tags' => [],
            'cuisines' => [],
            'utensils' => [],
            'label' => null,
            'canonical' => '',
            'createdAt' => now()->toIso8601String(),
            'updatedAt' => now()->toIso8601String(),
        ];
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $job = new ImportRecipeJob($this->country, 'en', $this->validRecipeData);

        $this->assertInstanceOf(ImportRecipeJob::class, $job);
    }

    #[Test]
    public function it_uses_import_queue(): void
    {
        $job = new ImportRecipeJob($this->country, 'en', $this->validRecipeData);

        $this->assertSame(QueueEnum::Import->value, $job->queue);
    }

    #[Test]
    public function it_stores_country_and_locale(): void
    {
        $job = new ImportRecipeJob($this->country, 'de', $this->validRecipeData);

        $this->assertTrue($job->country->is($this->country));
        $this->assertSame('de', $job->locale);
    }

    #[Test]
    public function it_stores_recipe_data(): void
    {
        $job = new ImportRecipeJob($this->country, 'en', $this->validRecipeData);

        $this->assertSame($this->validRecipeData, $job->recipe);
    }

    #[Test]
    public function it_imports_recipe_with_valid_data(): void
    {
        $job = new ImportRecipeJob($this->country, 'en', $this->validRecipeData);
        $job->handle();

        $this->assertDatabaseHas('recipes', [
            'country_id' => $this->country->id,
            'hellofresh_id' => 'hf-recipe-123',
            'difficulty' => 2,
            'prep_time' => 15,
            'total_time' => 30,
        ]);
    }

    #[Test]
    public function it_skips_inactive_recipes(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['active'] = false;

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_recipes_without_image(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['imagePath'] = '';

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_addon_recipes(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['isAddon'] = true;

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_unpublished_recipes(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['isPublished'] = false;

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_recipes_without_steps(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['steps'] = [];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_recipes_with_less_than_4_ingredients(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['ingredients'] = [
            ['id' => 'ing-1', 'name' => 'Ingredient 1', 'imagePath' => null],
            ['id' => 'ing-2', 'name' => 'Ingredient 2', 'imagePath' => null],
            ['id' => 'ing-3', 'name' => 'Ingredient 3', 'imagePath' => null],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_recipes_with_less_than_2_yields(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['yields'] = [
            ['yields' => 2, 'ingredients' => []],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_imports_recipes_with_2_yields(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['yields'] = [
            ['yields' => 2, 'ingredients' => []],
            ['yields' => 4, 'ingredients' => []],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_skips_recipes_with_zero_prep_and_total_time(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['prepTime'] = 'PT0S';
        $recipeData['totalTime'] = 'PT';

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
        ]);
    }

    #[Test]
    public function it_imports_recipe_with_zero_prep_but_nonzero_total_time(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['prepTime'] = 'PT';
        $recipeData['totalTime'] = 'PT30M';

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
            'prep_time' => 0,
            'total_time' => 30,
        ]);
    }

    #[Test]
    public function it_creates_ingredients_for_recipe(): void
    {
        $job = new ImportRecipeJob($this->country, 'en', $this->validRecipeData);
        $job->handle();

        $this->assertDatabaseHas('ingredients', [
            'country_id' => $this->country->id,
        ]);

        $this->assertSame(4, $this->country->ingredients()->count());
    }

    #[Test]
    public function it_syncs_allergens_to_recipe(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['allergens'] = [
            ['id' => 'allergen-1', 'name' => 'Gluten', 'iconPath' => null],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('allergens', [
            'country_id' => $this->country->id,
        ]);
    }

    #[Test]
    public function it_syncs_tags_to_recipe(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['tags'] = [
            ['id' => 'tag-1', 'name' => 'Vegetarian', 'displayLabel' => true],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('tags', [
            'country_id' => $this->country->id,
            'display_label' => true,
        ]);
    }

    #[Test]
    public function it_syncs_cuisines_with_icon_link(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['cuisines'] = [
            ['id' => 'cuisine-1', 'name' => 'Italian', 'iconLink' => 'icon.png'],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('cuisines', [
            'country_id' => $this->country->id,
            'icon_path' => 'icon.png',
        ]);
    }

    #[Test]
    public function it_skips_cuisines_without_icon_link(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['cuisines'] = [
            ['id' => 'cuisine-1', 'name' => 'Italian', 'iconLink' => null],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseMissing('cuisines', [
            'country_id' => $this->country->id,
        ]);
    }

    #[Test]
    public function it_syncs_utensils_to_recipe(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['utensils'] = [
            ['id' => 'utensil-1', 'name' => 'Pan', 'type' => 'cookware'],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('utensils', [
            'country_id' => $this->country->id,
            'type' => 'cookware',
        ]);
    }

    #[Test]
    public function it_creates_label_for_recipe(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['label'] = [
            'handle' => 'premium',
            'text' => 'Premium',
            'foregroundColor' => '#FFFFFF',
            'backgroundColor' => '#000000',
            'displayLabel' => true,
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $label = Label::where('country_id', $this->country->id)
            ->whereJsonContains('handles', 'premium')
            ->first();

        $this->assertInstanceOf(Label::class, $label);
        $this->assertTrue($label->display_label);
    }

    #[Test]
    public function it_normalizes_label_handle_by_removing_country_suffix(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['label'] = [
            'handle' => 'premium-de',
            'text' => 'Premium',
            'foregroundColor' => '#FFFFFF',
            'backgroundColor' => '#000000',
            'displayLabel' => true,
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $label = Label::whereJsonContains('handles', 'premium')->first();

        $this->assertInstanceOf(Label::class, $label);
    }

    #[Test]
    public function it_ignores_discount_labels(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['label'] = [
            'handle' => 'summer-discount',
            'text' => 'Summer Discount',
            'foregroundColor' => '#FFFFFF',
            'backgroundColor' => '#FF0000',
            'displayLabel' => true,
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $label = Label::whereJsonContains('handles', 'summer-discount')->first();

        $this->assertNotInstanceOf(stdClass::class, $label);
    }

    #[Test]
    public function it_ignores_sale_labels(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['label'] = [
            'handle' => 'holiday-sale',
            'text' => 'Holiday Sale',
            'foregroundColor' => '#FFFFFF',
            'backgroundColor' => '#FF0000',
            'displayLabel' => true,
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $label = Label::whereJsonContains('handles', 'holiday-sale')->first();

        $this->assertNotInstanceOf(stdClass::class, $label);
    }

    #[Test]
    public function it_parses_iso_duration_with_hours_and_minutes(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['prepTime'] = 'PT1H30M';

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
            'prep_time' => 90,
        ]);
    }

    #[Test]
    public function it_handles_empty_duration_string(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['prepTime'] = '';
        $recipeData['totalTime'] = 'PT30M';

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $this->assertDatabaseHas('recipes', [
            'hellofresh_id' => 'hf-recipe-123',
            'prep_time' => 0,
        ]);
    }

    #[Test]
    public function it_transforms_steps_correctly(): void
    {
        $recipeData = $this->validRecipeData;
        $recipeData['steps'] = [
            [
                'index' => 1,
                'instructionsMarkdown' => 'First step',
                'images' => [
                    ['path' => 'step1.jpg', 'caption' => 'Step 1 image'],
                ],
            ],
        ];

        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        $recipe = $this->country->recipes()->first();
        $steps = $recipe->steps_primary;

        $this->assertCount(1, $steps);
        $this->assertSame(1, $steps[0]['index']);
        $this->assertSame('First step', $steps[0]['instructions']);
        $this->assertCount(1, $steps[0]['images']);
    }

    #[Test]
    public function it_uses_secondary_suffix_for_non_primary_locale(): void
    {
        $recipeData = $this->validRecipeData;

        // First import with primary locale
        $job = new ImportRecipeJob($this->country, 'en', $recipeData);
        $job->handle();

        // Then import with secondary locale
        $recipeData['name'] = 'German Name';
        $job = new ImportRecipeJob($this->country, 'de', $recipeData);
        $job->handle();

        $recipe = $this->country->recipes()->first();

        $this->assertNotNull($recipe->steps_primary);
        $this->assertNotNull($recipe->steps_secondary);
    }
}
