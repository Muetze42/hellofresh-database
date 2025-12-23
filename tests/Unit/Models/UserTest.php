<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Favorite;
use App\Models\RecipeList;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_favorites_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(HasMany::class, $user->favorites());
    }

    #[Test]
    public function it_can_have_many_favorites(): void
    {
        $user = User::factory()->create();
        Favorite::factory()->count(3)->forUser($user)->create();

        $this->assertCount(3, $user->favorites);
        $this->assertInstanceOf(Favorite::class, $user->favorites->first());
    }

    #[Test]
    public function it_has_recipe_lists_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(HasMany::class, $user->recipeLists());
    }

    #[Test]
    public function it_can_have_many_recipe_lists(): void
    {
        $user = User::factory()->create();
        RecipeList::factory()->count(3)->forUser($user)->create();

        $this->assertCount(3, $user->recipeLists);
        $this->assertInstanceOf(RecipeList::class, $user->recipeLists->first());
    }

    #[Test]
    public function it_has_shopping_lists_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(HasMany::class, $user->shoppingLists());
    }

    #[Test]
    public function it_can_have_many_shopping_lists(): void
    {
        $user = User::factory()->create();
        ShoppingList::factory()->count(3)->forUser($user)->create();

        $this->assertCount(3, $user->shoppingLists);
        $this->assertInstanceOf(ShoppingList::class, $user->shoppingLists->first());
    }

    #[Test]
    public function it_hides_sensitive_attributes_on_serialization(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
            'remember_token' => 'token123',
        ]);

        $serialized = $user->toArray();

        $this->assertArrayNotHasKey('password', $serialized);
        $this->assertArrayNotHasKey('remember_token', $serialized);
    }

    #[Test]
    public function it_casts_password_to_hashed(): void
    {
        $user = User::factory()->create();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('password', $casts);
        $this->assertSame('hashed', $casts['password']);
    }

    #[Test]
    public function it_casts_email_verified_at_to_datetime(): void
    {
        $user = User::factory()->create();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertSame('datetime', $casts['email_verified_at']);
    }

    #[Test]
    public function it_casts_active_at_to_datetime(): void
    {
        $user = User::factory()->create();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('active_at', $casts);
        $this->assertSame('datetime', $casts['active_at']);
    }

    #[Test]
    public function it_casts_admin_to_boolean(): void
    {
        $user = User::factory()->create();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('admin', $casts);
        $this->assertSame('bool', $casts['admin']);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_can_create_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }
}
