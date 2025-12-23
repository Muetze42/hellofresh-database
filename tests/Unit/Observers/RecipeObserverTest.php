<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Models\Country;
use App\Models\Recipe;
use App\Observers\RecipeObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RecipeObserverTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create();
    }

    #[Test]
    public function it_sets_has_pdf_true_when_card_link_exists(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => ['en' => 'https://example.com/card.pdf'],
        ]);

        $this->assertTrue($recipe->has_pdf);
    }

    #[Test]
    public function it_sets_has_pdf_false_when_card_link_is_null(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => null,
        ]);

        $this->assertFalse($recipe->has_pdf);
    }

    #[Test]
    public function it_sets_has_pdf_false_when_card_link_is_empty_array(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => [],
        ]);

        $this->assertFalse($recipe->has_pdf);
    }

    #[Test]
    public function it_sets_has_pdf_false_when_card_link_has_only_null_values(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => ['en' => null, 'de' => null],
        ]);

        $this->assertFalse($recipe->has_pdf);
    }

    #[Test]
    public function it_sets_has_pdf_false_when_card_link_has_only_empty_strings(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => ['en' => '', 'de' => ''],
        ]);

        $this->assertFalse($recipe->has_pdf);
    }

    #[Test]
    public function it_sets_has_pdf_true_when_at_least_one_card_link_is_valid(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => ['en' => null, 'de' => 'https://example.com/card.pdf'],
        ]);

        $this->assertTrue($recipe->has_pdf);
    }

    #[Test]
    public function it_updates_has_pdf_on_save(): void
    {
        $recipe = Recipe::factory()->for($this->country)->create([
            'card_link' => null,
        ]);

        $this->assertFalse($recipe->has_pdf);

        $recipe->card_link = ['en' => 'https://example.com/card.pdf'];
        $recipe->save();

        $this->assertTrue($recipe->fresh()->has_pdf);
    }

    #[Test]
    public function observer_can_be_instantiated(): void
    {
        $observer = new RecipeObserver();

        $this->assertInstanceOf(RecipeObserver::class, $observer);
    }

    #[Test]
    public function saving_method_sets_has_pdf(): void
    {
        $recipe = new Recipe();
        $recipe->country_id = $this->country->id;
        $recipe->hellofresh_id = 'test-id';
        $recipe->card_link = ['en' => 'https://example.com/card.pdf'];

        $observer = new RecipeObserver();
        $observer->saving($recipe);

        $this->assertTrue($recipe->has_pdf);
    }
}
