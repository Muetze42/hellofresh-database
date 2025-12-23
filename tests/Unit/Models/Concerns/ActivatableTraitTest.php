<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Concerns;

use App\Models\Country;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ActivatableTraitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_active_to_fillable(): void
    {
        $tag = new Tag();
        $fillable = $tag->getFillable();

        $this->assertContains('active', $fillable);
    }

    #[Test]
    public function it_casts_active_to_boolean(): void
    {
        $tag = new Tag();
        $casts = $tag->getCasts();

        $this->assertArrayHasKey('active', $casts);
        $this->assertSame('bool', $casts['active']);
    }

    #[Test]
    public function active_scope_returns_only_active_records(): void
    {
        $country = Country::factory()->create();
        Tag::factory()->for($country)->create(['active' => true]);
        Tag::factory()->for($country)->create(['active' => true]);
        Tag::factory()->for($country)->create(['active' => false]);

        $activeTags = Tag::active()->get();

        $this->assertCount(2, $activeTags);
        $this->assertTrue($activeTags->every(fn (Tag $tag): bool => $tag->active));
    }

    #[Test]
    public function active_scope_excludes_inactive_records(): void
    {
        $country = Country::factory()->create();
        $inactiveTag = Tag::factory()->for($country)->create(['active' => false]);

        $activeTags = Tag::active()->get();

        $this->assertFalse($activeTags->contains($inactiveTag));
    }
}
