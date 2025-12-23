<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Label;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LabelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $label = Label::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $label->country());
        $this->assertInstanceOf(Country::class, $label->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $label = Label::factory()->create();

        $this->assertInstanceOf(HasMany::class, $label->recipes());
    }

    #[Test]
    public function it_can_have_many_recipes(): void
    {
        $country = Country::factory()->create();
        $label = Label::factory()->for($country)->create();
        Recipe::factory()->count(3)->for($country)->create([
            'label_id' => $label->id,
        ]);

        $this->assertCount(3, $label->recipes);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $label = new Label();

        $this->assertContains('name', $label->translatable);
    }

    #[Test]
    public function it_casts_display_label_to_boolean(): void
    {
        $label = Label::factory()->create(['display_label' => true]);

        $this->assertTrue($label->display_label);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $label = Label::factory()->create();
        $serialized = $label->toArray();

        $this->assertArrayNotHasKey('handles', $serialized);
        $this->assertArrayNotHasKey('foreground_color', $serialized);
        $this->assertArrayNotHasKey('background_color', $serialized);
        $this->assertArrayNotHasKey('display_label', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Label::factory()->for($country)->create(['active' => true]);
        Label::factory()->for($country)->create(['active' => false]);

        $activeLabels = Label::active()->get();

        $this->assertCount(1, $activeLabels);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $label = Label::factory()->create([
            'name' => ['en' => 'Premium'],
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
        ]);
    }

    #[Test]
    public function it_can_create_inactive_label(): void
    {
        $label = Label::factory()->inactive()->create();

        $this->assertFalse($label->active);
    }

    #[Test]
    public function it_can_create_label_with_display_label(): void
    {
        $label = Label::factory()->displayLabel()->create();

        $this->assertTrue($label->display_label);
    }
}
