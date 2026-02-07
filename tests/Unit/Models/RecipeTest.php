<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Utensil;
use App\Observers\RecipeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

final class RecipeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $recipe->country());
        $this->assertInstanceOf(Country::class, $recipe->country);
    }

    #[Test]
    public function it_has_canonical_relationship(): void
    {
        $canonical = Recipe::factory()->create();
        $variant = Recipe::factory()->variant($canonical)->create();

        $this->assertInstanceOf(BelongsTo::class, $variant->canonical());
        $this->assertTrue($variant->canonical->is($canonical));
    }

    #[Test]
    public function it_has_variants_relationship(): void
    {
        $canonical = Recipe::factory()->create();
        Recipe::factory()->variant($canonical)->count(3)->create();

        $this->assertInstanceOf(HasMany::class, $canonical->variants());
        $this->assertCount(3, $canonical->variants);
    }

    #[Test]
    public function it_has_ingredients_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->ingredients());
    }

    #[Test]
    public function it_can_have_many_ingredients(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $ingredients = Ingredient::factory()->count(3)->for($country)->create();

        $recipe->ingredients()->attach($ingredients);

        $this->assertCount(3, $recipe->ingredients);
    }

    #[Test]
    public function it_has_allergens_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->allergens());
    }

    #[Test]
    public function it_can_have_many_allergens(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $allergens = Allergen::factory()->count(3)->for($country)->create();

        $recipe->allergens()->attach($allergens);

        $this->assertCount(3, $recipe->allergens);
    }

    #[Test]
    public function it_has_tags_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->tags());
    }

    #[Test]
    public function it_can_have_many_tags(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $tags = Tag::factory()->count(3)->for($country)->create();

        $recipe->tags()->attach($tags);

        $this->assertCount(3, $recipe->tags);
    }

    #[Test]
    public function it_has_label_relationship(): void
    {
        $country = Country::factory()->create();
        $label = Label::factory()->for($country)->create();
        $recipe = Recipe::factory()->for($country)->create([
            'label_id' => $label->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $recipe->label());
        $this->assertTrue($recipe->label->is($label));
    }

    #[Test]
    public function it_has_cuisines_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->cuisines());
    }

    #[Test]
    public function it_can_have_many_cuisines(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $cuisines = Cuisine::factory()->count(3)->for($country)->create();

        $recipe->cuisines()->attach($cuisines);

        $this->assertCount(3, $recipe->cuisines);
    }

    #[Test]
    public function it_has_utensils_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->utensils());
    }

    #[Test]
    public function it_can_have_many_utensils(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $utensils = Utensil::factory()->count(3)->for($country)->create();

        $recipe->utensils()->attach($utensils);

        $this->assertCount(3, $recipe->utensils);
    }

    #[Test]
    public function it_has_menus_relationship(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $recipe->menus());
    }

    #[Test]
    public function it_can_belong_to_many_menus(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $menus = Menu::factory()->count(3)->for($country)->create();

        $recipe->menus()->attach($menus);

        $this->assertCount(3, $recipe->menus);
    }

    #[Test]
    public function it_casts_difficulty_to_integer(): void
    {
        $recipe = Recipe::factory()->create(['difficulty' => 2]);

        $this->assertIsInt($recipe->difficulty);
    }

    #[Test]
    public function it_casts_prep_time_to_integer(): void
    {
        $recipe = Recipe::factory()->create(['prep_time' => 15]);

        $this->assertIsInt($recipe->prep_time);
    }

    #[Test]
    public function it_casts_total_time_to_integer(): void
    {
        $recipe = Recipe::factory()->create(['total_time' => 30]);

        $this->assertIsInt($recipe->total_time);
    }

    #[Test]
    public function it_casts_steps_primary_to_array(): void
    {
        $steps = [
            ['instruction' => 'Step 1'],
            ['instruction' => 'Step 2'],
        ];
        $recipe = Recipe::factory()->create(['steps_primary' => $steps]);

        $this->assertIsArray($recipe->steps_primary);
        $this->assertCount(2, $recipe->steps_primary);
    }

    #[Test]
    public function it_casts_nutrition_primary_to_array(): void
    {
        $nutrition = [
            ['name' => 'Calories', 'amount' => 500],
        ];
        $recipe = Recipe::factory()->create(['nutrition_primary' => $nutrition]);

        $this->assertIsArray($recipe->nutrition_primary);
    }

    #[Test]
    public function it_casts_yields_primary_to_array(): void
    {
        $yields = [
            ['yields' => 2, 'ingredients' => []],
        ];
        $recipe = Recipe::factory()->create(['yields_primary' => $yields]);

        $this->assertIsArray($recipe->yields_primary);
    }

    #[Test]
    public function it_casts_has_pdf_to_boolean(): void
    {
        $recipe = Recipe::factory()->withPdf()->create();

        $this->assertIsBool($recipe->has_pdf);
        $this->assertTrue($recipe->has_pdf);
    }

    #[Test]
    public function it_has_translatable_attributes(): void
    {
        $recipe = new Recipe();

        $this->assertContains('name', $recipe->translatable);
        $this->assertContains('headline', $recipe->translatable);
        $this->assertContains('description', $recipe->translatable);
        $this->assertContains('card_link', $recipe->translatable);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $recipe = Recipe::factory()->create();
        $serialized = $recipe->toArray();

        $this->assertArrayNotHasKey('hellofresh_id', $serialized);
        $this->assertArrayNotHasKey('steps_primary', $serialized);
        $this->assertArrayNotHasKey('steps_secondary', $serialized);
        $this->assertArrayNotHasKey('nutrition_primary', $serialized);
        $this->assertArrayNotHasKey('nutrition_secondary', $serialized);
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $recipe = Recipe::factory()->create();
        $recipe->delete();

        $this->assertSoftDeleted($recipe);
        $this->assertNotNull($recipe->deleted_at);
    }

    #[Test]
    public function it_can_get_first_translation(): void
    {
        $recipe = Recipe::factory()->create([
            'name' => ['en' => 'English Name', 'de' => 'German Name'],
        ]);

        $firstTranslation = $recipe->getFirstTranslation('name');

        $this->assertSame('English Name', $firstTranslation);
    }

    #[Test]
    public function get_first_translation_returns_null_for_empty_translations(): void
    {
        $recipe = Recipe::factory()->create(['name' => []]);

        $firstTranslation = $recipe->getFirstTranslation('name');

        $this->assertNull($firstTranslation);
    }

    #[Test]
    public function it_generates_card_image_url_attribute(): void
    {
        $recipe = Recipe::factory()->create(['image_path' => 'test/image.jpg']);

        $this->assertNotNull($recipe->card_image_url);
    }

    #[Test]
    public function card_image_url_is_null_without_image_path(): void
    {
        $recipe = Recipe::factory()->create(['image_path' => null]);

        $this->assertNull($recipe->card_image_url);
    }

    #[Test]
    public function it_generates_header_image_url_attribute(): void
    {
        $recipe = Recipe::factory()->create(['image_path' => 'test/image.jpg']);

        $this->assertNotNull($recipe->header_image_url);
    }

    #[Test]
    public function header_image_url_is_null_without_image_path(): void
    {
        $recipe = Recipe::factory()->create(['image_path' => null]);

        $this->assertNull($recipe->header_image_url);
    }

    #[Test]
    public function it_generates_hellofresh_url_with_country_loaded(): void
    {
        $country = Country::factory()->create(['domain' => 'https://www.hellofresh.com']);
        $recipe = Recipe::factory()->for($country)->create([
            'hellofresh_id' => 'abc123',
            'name' => ['en' => 'Test Recipe'],
            'published' => true,
        ]);
        $recipe->load('country');

        app()->setLocale('en');

        $this->assertStringContainsString('hellofresh.com', (string) $recipe->hellofresh_url);
        $this->assertStringContainsString('abc123', (string) $recipe->hellofresh_url);
    }

    #[Test]
    public function hellofresh_url_is_null_without_country_loaded(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertNull($recipe->hellofresh_url);
    }

    #[Test]
    public function hellofresh_url_is_null_without_hellofresh_id(): void
    {
        $recipe = Recipe::factory()->create(['hellofresh_id' => '']);
        $recipe->load('country');

        $this->assertNull($recipe->hellofresh_url);
    }

    #[Test]
    public function it_generates_pdf_url_from_card_link(): void
    {
        $recipe = Recipe::factory()->create([
            'card_link' => ['en' => 'https://example.com/recipe.pdf'],
        ]);
        app()->setLocale('en');

        $this->assertSame('https://example.com/recipe.pdf', $recipe->pdf_url);
    }

    #[Test]
    public function pdf_url_returns_null_when_no_card_link(): void
    {
        $recipe = Recipe::factory()->create(['card_link' => null]);

        $this->assertNull($recipe->pdf_url);
    }

    #[Test]
    public function pdf_url_falls_back_to_first_translation(): void
    {
        $recipe = Recipe::factory()->create([
            'card_link' => ['de' => 'https://example.com/recipe-de.pdf'],
        ]);
        app()->setLocale('en');

        $this->assertSame('https://example.com/recipe-de.pdf', $recipe->pdf_url);
    }

    #[Test]
    public function it_is_observed_by_recipe_observer(): void
    {
        $reflectionClass = new ReflectionClass(Recipe::class);
        $attributes = $reflectionClass->getAttributes(ObservedBy::class);

        $this->assertCount(1, $attributes);
        $observerClasses = $attributes[0]->getArguments()[0];
        $this->assertContains(RecipeObserver::class, $observerClasses);
    }

    #[Test]
    public function it_can_create_recipe_with_pdf(): void
    {
        $recipe = Recipe::factory()->withPdf()->create();

        $this->assertTrue($recipe->has_pdf);
        $this->assertNotNull($recipe->card_link);
    }

    #[Test]
    public function it_can_create_variant_recipe(): void
    {
        $canonical = Recipe::factory()->create();
        $variant = Recipe::factory()->variant($canonical)->create();

        $this->assertSame($canonical->id, $variant->canonical_id);
        $this->assertSame($canonical->country_id, $variant->country_id);
    }
}
