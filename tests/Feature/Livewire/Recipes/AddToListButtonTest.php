<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Recipes;

use App\Events\RecipeListUpdatedEvent;
use App\Livewire\Web\Recipes\AddToListButton;
use App\Models\Country;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AddToListButtonTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    private Recipe $recipe;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $this->recipe = Recipe::factory()->for($this->country)->create();

        app()->bind('current.country', fn (): Country => $this->country);
        app()->setLocale('en');
    }

    #[Test]
    public function it_can_render(): void
    {
        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->assertStatus(200);
    }

    #[Test]
    public function it_stores_recipe_id(): void
    {
        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertSame($this->recipe->id, $component->get('recipeId'));
    }

    #[Test]
    public function it_returns_empty_lists_for_guest(): void
    {
        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $lists = $component->instance()->lists();

        $this->assertCount(0, $lists);
    }

    #[Test]
    public function it_returns_empty_selected_lists_for_guest(): void
    {
        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertSame([], $component->get('selectedLists'));
    }

    #[Test]
    public function it_returns_user_lists_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        RecipeList::factory()->for($user)->create(['name' => 'My List']);

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $lists = $component->instance()->lists();

        $this->assertCount(1, $lists);
        $this->assertSame('My List', $lists->first()->name);
    }

    #[Test]
    public function it_orders_lists_by_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        RecipeList::factory()->for($user)->create(['name' => 'Zebra']);
        RecipeList::factory()->for($user)->create(['name' => 'Apple']);

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $lists = $component->instance()->lists();

        $this->assertSame('Apple', $lists->first()->name);
        $this->assertSame('Zebra', $lists->last()->name);
    }

    #[Test]
    public function it_loads_selected_lists_on_mount(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();
        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertContains($list->id, $component->get('selectedLists'));
    }

    #[Test]
    public function is_in_any_list_returns_true_when_recipe_in_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();
        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertTrue($component->instance()->isInAnyList());
    }

    #[Test]
    public function is_in_any_list_returns_false_when_recipe_not_in_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        RecipeList::factory()->for($user)->create();

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertFalse($component->instance()->isInAnyList());
    }

    #[Test]
    public function it_adds_recipe_to_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('selectedLists', [$list->id])
            ->call('saveLists');

        $this->assertTrue($list->recipes()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function it_removes_recipe_from_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();
        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('selectedLists', [])
            ->call('saveLists');

        $this->assertFalse($list->fresh()->recipes()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function updated_selected_lists_does_nothing_for_guest(): void
    {
        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);
        $component->set('selectedLists', [999]);

        $this->assertDatabaseMissing('recipe_recipe_list', ['recipe_id' => $this->recipe->id]);
    }

    #[Test]
    public function it_creates_new_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'New List Name')
            ->call('createList');

        $this->assertDatabaseHas('recipe_lists', [
            'user_id' => $user->id,
            'name' => 'New List Name',
        ]);
    }

    #[Test]
    public function it_adds_recipe_to_newly_created_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'New List')
            ->call('createList');

        $list = RecipeList::where('name', 'New List')->first();
        $this->assertInstanceOf(RecipeList::class, $list);

        $this->assertTrue($list->recipes()->where('recipe_id', $this->recipe->id)->exists());
    }

    #[Test]
    public function it_clears_search_after_creating_list(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'New List')
            ->call('createList')
            ->assertSet('search', '');
    }

    #[Test]
    public function create_list_does_nothing_for_short_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'X')
            ->call('createList');

        $this->assertDatabaseMissing('recipe_lists', ['name' => 'X']);
    }

    #[Test]
    public function create_list_dispatches_require_auth_for_guest(): void
    {
        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'My List')
            ->call('createList')
            ->assertDispatched('require-auth');
    }

    #[Test]
    public function open_modal_dispatches_require_auth_for_guest(): void
    {
        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->call('openModal')
            ->assertDispatched('require-auth');
    }

    #[Test]
    public function refresh_lists_reloads_selected_lists(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertSame([], $component->get('selectedLists'));

        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $component->dispatch('user-authenticated');

        $this->assertContains($list->id, $component->get('selectedLists'));
    }

    #[Test]
    public function it_dispatches_event_when_saving_lists(): void
    {
        Event::fake([RecipeListUpdatedEvent::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('selectedLists', [$list->id])
            ->call('saveLists');

        Event::assertDispatched(function (RecipeListUpdatedEvent $event) use ($user): bool {
            return $event->userId === $user->id
                && $event->recipeId === $this->recipe->id
                && $event->countryId === $this->country->id;
        });
    }

    #[Test]
    public function it_dispatches_event_when_creating_list(): void
    {
        Event::fake([RecipeListUpdatedEvent::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->set('search', 'New List Name')
            ->call('createList');

        Event::assertDispatched(function (RecipeListUpdatedEvent $event) use ($user): bool {
            return $event->userId === $user->id
                && $event->recipeId === $this->recipe->id
                && $event->countryId === $this->country->id;
        });
    }

    #[Test]
    public function it_does_not_dispatch_event_when_no_changes_made(): void
    {
        Event::fake([RecipeListUpdatedEvent::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        RecipeList::factory()->for($user)->create();

        Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id])
            ->call('saveLists');

        Event::assertNotDispatched(RecipeListUpdatedEvent::class);
    }

    #[Test]
    public function handle_recipe_list_updated_refreshes_component(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $this->assertSame([], $component->get('selectedLists'));

        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $component->call('handleRecipeListUpdated', [
            'recipeId' => $this->recipe->id,
            'countryId' => $this->country->id,
        ]);

        $this->assertContains($list->id, $component->get('selectedLists'));
    }

    #[Test]
    public function handle_recipe_list_updated_ignores_different_recipe(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $otherRecipe = Recipe::factory()->for($this->country)->create();

        $component->call('handleRecipeListUpdated', [
            'recipeId' => $otherRecipe->id,
            'countryId' => $this->country->id,
        ]);

        $this->assertSame([], $component->get('selectedLists'));
    }

    #[Test]
    public function handle_recipe_list_updated_ignores_different_country(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $list = RecipeList::factory()->for($user)->create();

        $component = Livewire::test(AddToListButton::class, ['recipeId' => $this->recipe->id]);

        $list->recipes()->attach($this->recipe->id, ['added_at' => now(), 'country_id' => $this->country->id]);

        $otherCountry = Country::factory()->create(['code' => 'DE']);

        $component->call('handleRecipeListUpdated', [
            'recipeId' => $this->recipe->id,
            'countryId' => $otherCountry->id,
        ]);

        $this->assertSame([], $component->get('selectedLists'));
    }
}
