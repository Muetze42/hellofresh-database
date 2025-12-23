<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TagTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_country_relationship(): void
    {
        $tag = Tag::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $tag->country());
        $this->assertInstanceOf(Country::class, $tag->country);
    }

    #[Test]
    public function it_has_recipes_relationship(): void
    {
        $tag = Tag::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $tag->recipes());
    }

    #[Test]
    public function it_can_belong_to_many_recipes(): void
    {
        $country = Country::factory()->create();
        $tag = Tag::factory()->for($country)->create();
        $recipes = Recipe::factory()->count(3)->for($country)->create();

        $tag->recipes()->attach($recipes);

        $this->assertCount(3, $tag->recipes);
    }

    #[Test]
    public function it_has_translatable_name(): void
    {
        $tag = new Tag();

        $this->assertContains('name', $tag->translatable);
    }

    #[Test]
    public function it_casts_display_label_to_boolean(): void
    {
        $tag = Tag::factory()->create(['display_label' => true]);

        $this->assertTrue($tag->display_label);
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $tag = Tag::factory()->create();
        $serialized = $tag->toArray();

        $this->assertArrayNotHasKey('hellofresh_ids', $serialized);
        $this->assertArrayNotHasKey('display_label', $serialized);
    }

    #[Test]
    public function it_has_active_scope(): void
    {
        $country = Country::factory()->create();
        Tag::factory()->for($country)->create(['active' => true]);
        Tag::factory()->for($country)->create(['active' => false]);

        $activeTags = Tag::active()->get();

        $this->assertCount(1, $activeTags);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $tag = Tag::factory()->create([
            'name' => ['en' => 'Vegetarian'],
        ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
        ]);
    }

    #[Test]
    public function it_can_create_inactive_tag(): void
    {
        $tag = Tag::factory()->inactive()->create();

        $this->assertFalse($tag->active);
    }

    #[Test]
    public function it_can_create_tag_with_display_label(): void
    {
        $tag = Tag::factory()->displayLabel()->create();

        $this->assertTrue($tag->display_label);
    }
}
