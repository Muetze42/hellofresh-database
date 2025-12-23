<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Favorite;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_user_relationship(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $favorite->user());
        $this->assertInstanceOf(User::class, $favorite->user);
    }

    #[Test]
    public function it_has_country_relationship(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $favorite->country());
        $this->assertInstanceOf(Country::class, $favorite->country);
    }

    #[Test]
    public function it_has_recipe_relationship(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $favorite->recipe());
        $this->assertInstanceOf(Recipe::class, $favorite->recipe);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $favorite = Favorite::factory()->create();

        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id,
        ]);
    }

    #[Test]
    public function it_can_be_created_for_specific_user(): void
    {
        $user = User::factory()->create();
        $favorite = Favorite::factory()->forUser($user)->create();

        $this->assertTrue($favorite->user->is($user));
    }

    #[Test]
    public function it_can_be_created_for_specific_recipe(): void
    {
        $country = Country::factory()->create();
        $recipe = Recipe::factory()->for($country)->create();
        $favorite = Favorite::factory()->forRecipe($recipe)->create();

        $this->assertTrue($favorite->recipe->is($recipe));
        $this->assertSame($recipe->country_id, $favorite->country_id);
    }

    #[Test]
    public function it_has_empty_fillable_array(): void
    {
        $favorite = new Favorite();

        $this->assertSame([], $favorite->getFillable());
    }
}
