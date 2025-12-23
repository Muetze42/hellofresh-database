<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_user_relationship(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $shoppingList->user());
        $this->assertInstanceOf(User::class, $shoppingList->user);
    }

    #[Test]
    public function it_has_country_relationship(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $shoppingList->country());
        $this->assertInstanceOf(Country::class, $shoppingList->country);
    }

    #[Test]
    public function it_casts_items_to_array(): void
    {
        $items = [
            ['recipe_id' => 1, 'servings' => 2],
            ['recipe_id' => 2, 'servings' => 4],
        ];
        $shoppingList = ShoppingList::factory()->withItems($items)->create();

        $this->assertIsArray($shoppingList->items);
        $this->assertCount(2, $shoppingList->items);
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $shoppingList = new ShoppingList();
        $fillable = $shoppingList->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('items', $fillable);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        ShoppingList::factory()->create([
            'name' => 'Weekly Shopping',
        ]);

        $this->assertDatabaseHas('shopping_lists', [
            'name' => 'Weekly Shopping',
        ]);
    }

    #[Test]
    public function it_can_be_created_for_specific_user(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->forUser($user)->create();

        $this->assertTrue($shoppingList->user->is($user));
    }

    #[Test]
    public function it_can_be_created_for_specific_country(): void
    {
        $country = Country::factory()->create();
        $shoppingList = ShoppingList::factory()->forCountry($country)->create();

        $this->assertTrue($shoppingList->country->is($country));
    }

    #[Test]
    public function it_can_be_created_with_items(): void
    {
        $items = [
            ['recipe_id' => 1, 'servings' => 2],
            ['recipe_id' => 2, 'servings' => 4],
        ];
        $shoppingList = ShoppingList::factory()->withItems($items)->create();

        $this->assertSame(1, $shoppingList->items[0]['recipe_id']);
        $this->assertSame(2, $shoppingList->items[0]['servings']);
    }

    #[Test]
    public function it_defaults_to_empty_items_array(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $this->assertIsArray($shoppingList->items);
        $this->assertEmpty($shoppingList->items);
    }
}
