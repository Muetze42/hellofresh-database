<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\User;

use App\Livewire\Web\User\UserRecipeLists;
use App\Models\Country;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\User;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserRecipeListsTest extends TestCase
{
    private Country $country;

    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $this->user = User::factory()->create();

        app()->bind('current.country', fn (): Country => $this->country);
    }

    #[Test]
    public function it_renders_user_recipe_lists_component(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->assertOk()
            ->assertViewIs('livewire.user.user-recipe-lists');
    }

    #[Test]
    public function it_returns_empty_collection_for_guest(): void
    {
        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(0, $component->instance()->recipeLists());
    }

    #[Test]
    public function it_returns_user_recipe_lists(): void
    {
        $this->actingAs($this->user);

        RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->count(3)
            ->create();

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(3, $component->instance()->recipeLists());
    }

    #[Test]
    public function it_only_returns_lists_for_current_country(): void
    {
        $this->actingAs($this->user);

        RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        $otherCountry = Country::factory()->create(['code' => 'DE', 'locales' => ['de']]);
        RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($otherCountry)
            ->create();

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(1, $component->instance()->recipeLists());
    }

    #[Test]
    public function it_orders_lists_by_name(): void
    {
        $this->actingAs($this->user);

        RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create(['name' => 'Zebra']);
        RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create(['name' => 'Apple']);
        RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create(['name' => 'Mango']);

        $component = Livewire::test(UserRecipeLists::class);
        $lists = $component->instance()->recipeLists();

        $this->assertEquals('Apple', $lists->values()[0]->name);
        $this->assertEquals('Mango', $lists->values()[1]->name);
        $this->assertEquals('Zebra', $lists->values()[2]->name);
    }

    #[Test]
    public function it_includes_recipes_count_on_lists(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create();
        $recipes = Recipe::factory()->for($this->country)->count(5)->create();
        $list->recipes()->attach($recipes->pluck('id'));

        $component = Livewire::test(UserRecipeLists::class);
        $lists = $component->instance()->recipeLists();

        $this->assertEquals(5, $lists->first()->recipes_count);
    }

    #[Test]
    public function it_creates_new_list(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'My New List')
            ->set('newListDescription', 'Description here')
            ->call('createList');

        $this->assertDatabaseHas('recipe_lists', [
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
            'name' => 'My New List',
            'description' => 'Description here',
        ]);
    }

    #[Test]
    public function it_resets_form_after_creating_list(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'My New List')
            ->set('newListDescription', 'Description')
            ->call('createList')
            ->assertSet('newListName', '')
            ->assertSet('newListDescription', '');
    }

    #[Test]
    public function it_validates_list_name_when_creating(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', '')
            ->call('createList')
            ->assertHasErrors(['newListName' => 'required']);
    }

    #[Test]
    public function it_validates_list_name_min_length_when_creating(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'A')
            ->call('createList')
            ->assertHasErrors(['newListName' => 'min']);
    }

    #[Test]
    public function it_validates_list_name_max_length_when_creating(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', str_repeat('a', 256))
            ->call('createList')
            ->assertHasErrors(['newListName' => 'max']);
    }

    #[Test]
    public function it_validates_description_max_length_when_creating(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'Valid Name')
            ->set('newListDescription', str_repeat('a', 1001))
            ->call('createList')
            ->assertHasErrors(['newListDescription' => 'max']);
    }

    #[Test]
    public function it_does_not_create_list_for_guest(): void
    {
        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'My List')
            ->call('createList');

        $this->assertDatabaseCount('recipe_lists', 0);
    }

    #[Test]
    public function it_starts_editing_a_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create([
                'name' => 'Original Name',
                'description' => 'Original Description',
            ]);

        Livewire::test(UserRecipeLists::class)
            ->call('startEditing', $list->id)
            ->assertSet('editingListId', $list->id)
            ->assertSet('editListName', 'Original Name')
            ->assertSet('editListDescription', 'Original Description');
    }

    #[Test]
    public function it_handles_start_editing_non_existent_list(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->call('startEditing', 99999)
            ->assertSet('editingListId', null);
    }

    #[Test]
    public function it_handles_editing_list_with_null_description(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->withoutDescription()
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->call('startEditing', $list->id)
            ->assertSet('editListDescription', '');
    }

    #[Test]
    public function it_updates_a_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'Updated Name')
            ->set('editListDescription', 'Updated Description')
            ->call('updateList');

        $this->assertDatabaseHas('recipe_lists', [
            'id' => $list->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    #[Test]
    public function it_resets_form_after_updating_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'Updated Name')
            ->set('editListDescription', 'Updated Desc')
            ->call('updateList')
            ->assertSet('editingListId', null)
            ->assertSet('editListName', '')
            ->assertSet('editListDescription', '');
    }

    #[Test]
    public function it_validates_list_name_when_updating(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', '')
            ->call('updateList')
            ->assertHasErrors(['editListName' => 'required']);
    }

    #[Test]
    public function it_handles_update_non_existent_list(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', 99999)
            ->set('editListName', 'Name')
            ->call('updateList')
            ->assertOk();
    }

    #[Test]
    public function it_deletes_a_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->call('deleteList', $list->id);

        $this->assertDatabaseMissing('recipe_lists', ['id' => $list->id]);
    }

    #[Test]
    public function it_clears_viewing_list_id_when_deleting_viewed_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('deleteList', $list->id)
            ->assertSet('viewingListId', null);
    }

    #[Test]
    public function it_keeps_viewing_list_id_when_deleting_different_list(): void
    {
        $this->actingAs($this->user);

        $list1 = RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create();
        $list2 = RecipeList::factory()->forUser($this->user)->forCountry($this->country)->create();

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list1->id)
            ->call('deleteList', $list2->id)
            ->assertSet('viewingListId', $list1->id);
    }

    #[Test]
    public function it_views_a_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->call('viewList', $list->id)
            ->assertSet('viewingListId', $list->id);
    }

    #[Test]
    public function it_returns_to_lists_overview(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('backToLists')
            ->assertSet('viewingListId', null);
    }

    #[Test]
    public function it_returns_null_viewing_list_when_no_id(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertNull($component->instance()->viewingList());
    }

    #[Test]
    public function it_returns_viewing_list_with_recipes(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe);

        $component = Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id);

        $viewingList = $component->instance()->viewingList();

        $this->assertEquals($list->id, $viewingList->id);
        $this->assertTrue($viewingList->relationLoaded('recipes'));
    }

    #[Test]
    public function it_removes_recipe_from_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe);

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('removeRecipeFromList', $recipe->id);

        $this->assertDatabaseMissing('recipe_recipe_list', [
            'recipe_list_id' => $list->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    #[Test]
    public function it_does_not_remove_recipe_when_no_viewing_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe);

        Livewire::test(UserRecipeLists::class)
            ->call('removeRecipeFromList', $recipe->id);

        $this->assertDatabaseHas('recipe_recipe_list', [
            'recipe_list_id' => $list->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    #[Test]
    public function it_validates_edit_list_name_min_length(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'A')
            ->call('updateList')
            ->assertHasErrors(['editListName' => 'min']);
    }

    #[Test]
    public function it_validates_edit_list_name_max_length(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', str_repeat('a', 256))
            ->call('updateList')
            ->assertHasErrors(['editListName' => 'max']);
    }

    #[Test]
    public function it_validates_edit_description_max_length(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create();

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'Valid Name')
            ->set('editListDescription', str_repeat('a', 1001))
            ->call('updateList')
            ->assertHasErrors(['editListDescription' => 'max']);
    }

    #[Test]
    public function it_allows_nullable_description_when_creating(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('newListName', 'List Without Description')
            ->set('newListDescription', '')
            ->call('createList')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('recipe_lists', [
            'name' => 'List Without Description',
        ]);
    }

    #[Test]
    public function it_allows_nullable_description_when_updating(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->forCountry($this->country)
            ->create(['description' => 'Original']);

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'Updated Name')
            ->set('editListDescription', '')
            ->call('updateList')
            ->assertHasNoErrors();
    }
}
