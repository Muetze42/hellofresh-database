<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Jobs\Country\UpdateCountryStatisticsJob;
use App\Models\Allergen;
use App\Models\Country;
use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Utensil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpdateCountryStatisticsJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $job = new UpdateCountryStatisticsJob();

        $this->assertInstanceOf(UpdateCountryStatisticsJob::class, $job);
    }

    #[Test]
    public function it_can_be_instantiated_with_country(): void
    {
        $country = Country::factory()->create();
        $job = new UpdateCountryStatisticsJob($country);

        $this->assertInstanceOf(UpdateCountryStatisticsJob::class, $job);
    }

    #[Test]
    public function it_uses_long_queue(): void
    {
        $job = new UpdateCountryStatisticsJob();

        $this->assertSame(QueueEnum::Long->value, $job->queue);
    }

    #[Test]
    public function it_updates_statistics_for_single_country(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->for($country)->count(5)->create([
            'prep_time' => 15,
            'total_time' => 30,
        ]);
        Ingredient::factory()->for($country)->count(10)->create();

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertSame(5, $country->recipes_count);
        $this->assertSame(10, $country->ingredients_count);
        $this->assertSame(15, $country->prep_min);
        $this->assertSame(15, $country->prep_max);
        $this->assertSame(30, $country->total_min);
        $this->assertSame(30, $country->total_max);
    }

    #[Test]
    public function it_updates_statistics_for_all_countries(): void
    {
        $country1 = Country::factory()->create();
        $country2 = Country::factory()->create();
        Recipe::factory()->for($country1)->count(3)->create(['prep_time' => 10, 'total_time' => 20]);
        Recipe::factory()->for($country2)->count(5)->create(['prep_time' => 15, 'total_time' => 30]);

        $job = new UpdateCountryStatisticsJob();
        $job->handle();

        $country1->refresh();
        $country2->refresh();

        $this->assertSame(3, $country1->recipes_count);
        $this->assertSame(5, $country2->recipes_count);
    }

    #[Test]
    public function it_calculates_min_and_max_prep_times(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->for($country)->create(['prep_time' => 10, 'total_time' => 20]);
        Recipe::factory()->for($country)->create(['prep_time' => 30, 'total_time' => 60]);
        Recipe::factory()->for($country)->create(['prep_time' => 20, 'total_time' => 40]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertSame(10, $country->prep_min);
        $this->assertSame(30, $country->prep_max);
        $this->assertSame(20, $country->total_min);
        $this->assertSame(60, $country->total_max);
    }

    #[Test]
    public function it_ignores_zero_prep_times_for_min_max(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->for($country)->create(['prep_time' => 0, 'total_time' => 20]);
        Recipe::factory()->for($country)->create(['prep_time' => 15, 'total_time' => 30]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertSame(15, $country->prep_min);
        $this->assertSame(15, $country->prep_max);
    }

    #[Test]
    public function it_sets_null_when_no_valid_prep_times(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->for($country)->create(['prep_time' => 0, 'total_time' => 0]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertNull($country->prep_min);
        $this->assertNull($country->prep_max);
    }

    #[Test]
    public function it_updates_has_allergens_flag(): void
    {
        $country = Country::factory()->create();
        $allergens = Allergen::factory()->for($country)->count(5)->create(['active' => true]);
        $recipes = Recipe::factory()->for($country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $recipe->allergens()->attach($allergens);
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertTrue($country->has_allergens);
    }

    #[Test]
    public function it_sets_has_allergens_false_when_less_than_4(): void
    {
        $country = Country::factory()->create();
        Allergen::factory()->for($country)->count(3)->create(['active' => true]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertFalse($country->has_allergens);
    }

    #[Test]
    public function it_updates_has_cuisines_flag(): void
    {
        $country = Country::factory()->create();
        $cuisines = Cuisine::factory()->for($country)->count(5)->create(['active' => true]);
        $recipes = Recipe::factory()->for($country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $recipe->cuisines()->attach($cuisines);
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertTrue($country->has_cuisines);
    }

    #[Test]
    public function it_updates_has_labels_flag(): void
    {
        $country = Country::factory()->create();
        $labels = Label::factory()->for($country)->count(5)->create(['active' => true]);
        foreach ($labels as $label) {
            Recipe::factory()->for($country)->count(5)->create(['label_id' => $label->id]);
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertTrue($country->has_labels);
    }

    #[Test]
    public function it_updates_has_tags_flag(): void
    {
        $country = Country::factory()->create();
        $tags = Tag::factory()->for($country)->count(5)->create(['active' => true]);
        $recipes = Recipe::factory()->for($country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $recipe->tags()->attach($tags);
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertTrue($country->has_tags);
    }

    #[Test]
    public function it_updates_has_utensil_flag(): void
    {
        $country = Country::factory()->create();
        $utensils = Utensil::factory()->for($country)->count(5)->create(['active' => true]);
        $recipes = Recipe::factory()->for($country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $recipe->utensils()->attach($utensils);
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertTrue($country->has_utensil);
    }

    #[Test]
    public function it_ignores_soft_deleted_recipes_in_count(): void
    {
        $country = Country::factory()->create();
        $allergen = Allergen::factory()->for($country)->create(['active' => false]);
        $recipes = Recipe::factory()->for($country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $recipe->allergens()->attach($allergen);
        }

        // Soft delete all recipes
        foreach ($recipes as $recipe) {
            $recipe->delete();
        }

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $allergen->refresh();
        $this->assertFalse($allergen->active);
    }

    #[Test]
    public function it_sets_counts_to_null_when_zero(): void
    {
        $country = Country::factory()->create(['recipes_count' => 10, 'ingredients_count' => 10]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertNull($country->recipes_count);
        $this->assertNull($country->ingredients_count);
    }

    #[Test]
    public function it_counts_recipes_with_pdf(): void
    {
        $country = Country::factory()->create();
        Recipe::factory()->for($country)->count(3)->create(['has_pdf' => false]);
        Recipe::factory()->for($country)->withPdf()->count(2)->create();

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertSame(5, $country->recipes_count);
        $this->assertSame(2, $country->recipes_with_pdf_count);
    }

    #[Test]
    public function it_sets_recipes_with_pdf_count_to_null_when_zero(): void
    {
        $country = Country::factory()->create(['recipes_with_pdf_count' => 10]);
        Recipe::factory()->for($country)->count(3)->create(['has_pdf' => false]);

        $job = new UpdateCountryStatisticsJob($country);
        $job->handle();

        $country->refresh();
        $this->assertSame(3, $country->recipes_count);
        $this->assertNull($country->recipes_with_pdf_count);
    }
}
