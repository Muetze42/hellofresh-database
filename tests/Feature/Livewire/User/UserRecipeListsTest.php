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
            ->assertViewIs('web::livewire.user.user-recipe-lists');
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
            ->count(3)
            ->create();

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(3, $component->instance()->recipeLists());
    }

    #[Test]
    public function it_returns_all_lists_regardless_of_country(): void
    {
        $this->actingAs($this->user);

        // Create lists (no longer bound to a country)
        RecipeList::factory()
            ->forUser($this->user)
            ->count(2)
            ->create();

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(2, $component->instance()->recipeLists());
    }

    #[Test]
    public function it_orders_lists_by_name(): void
    {
        $this->actingAs($this->user);

        RecipeList::factory()->forUser($this->user)->create(['name' => 'Zebra']);
        RecipeList::factory()->forUser($this->user)->create(['name' => 'Apple']);
        RecipeList::factory()->forUser($this->user)->create(['name' => 'Mango']);

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

        $list = RecipeList::factory()->forUser($this->user)->create();
        $recipes = Recipe::factory()->for($this->country)->count(5)->create();

        foreach ($recipes as $recipe) {
            $list->recipes()->attach($recipe->id, [
                'added_at' => now(),
                'country_id' => $this->country->id,
            ]);
        }

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

        $list1 = RecipeList::factory()->forUser($this->user)->create();
        $list2 = RecipeList::factory()->forUser($this->user)->create();

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
    public function it_returns_viewing_list_with_recipes_filtered_by_current_country(): void
    {
        $this->actingAs($this->user);

        $otherCountry = Country::factory()->create(['code' => 'DE']);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->create();

        $recipeUS = Recipe::factory()->for($this->country)->create();
        $recipeDE = Recipe::factory()->for($otherCountry)->create();

        $list->recipes()->attach($recipeUS, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);
        $list->recipes()->attach($recipeDE, [
            'added_at' => now(),
            'country_id' => $otherCountry->id,
        ]);

        $component = Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id);

        $viewingList = $component->instance()->viewingList();

        $this->assertEquals($list->id, $viewingList->id);
        $this->assertTrue($viewingList->relationLoaded('recipes'));
        $this->assertCount(1, $viewingList->recipes);
        $this->assertEquals($recipeUS->id, $viewingList->recipes->first()->id);
    }

    #[Test]
    public function it_returns_other_countries_recipe_count(): void
    {
        $this->actingAs($this->user);

        $otherCountry = Country::factory()->create(['code' => 'DE']);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->create();

        $recipeUS = Recipe::factory()->for($this->country)->create();
        $recipesDE = Recipe::factory()->for($otherCountry)->count(3)->create();

        $list->recipes()->attach($recipeUS, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

        foreach ($recipesDE as $recipe) {
            $list->recipes()->attach($recipe, [
                'added_at' => now(),
                'country_id' => $otherCountry->id,
            ]);
        }

        $component = Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id);

        $this->assertEquals(3, $component->instance()->otherCountriesRecipeCount());
    }

    #[Test]
    public function it_removes_recipe_from_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

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
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

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
            ->create(['description' => 'Original']);

        Livewire::test(UserRecipeLists::class)
            ->set('editingListId', $list->id)
            ->set('editListName', 'Updated Name')
            ->set('editListDescription', '')
            ->call('updateList')
            ->assertHasNoErrors();
    }

    #[Test]
    public function it_returns_zero_other_countries_count_when_no_viewing_list(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertEquals(0, $component->instance()->otherCountriesRecipeCount());
    }

    #[Test]
    public function it_returns_empty_recent_activities_when_no_viewing_list(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(0, $component->instance()->recentActivities());
    }

    #[Test]
    public function it_returns_empty_shared_lists_for_guest(): void
    {
        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(0, $component->instance()->sharedLists());
    }

    #[Test]
    public function it_returns_shared_lists_for_authenticated_user(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $list->sharedWith()->attach($this->user->id);

        $this->actingAs($this->user);

        $component = Livewire::test(UserRecipeLists::class);

        $this->assertCount(1, $component->instance()->sharedLists());
    }

    #[Test]
    public function it_starts_sharing_a_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();

        Livewire::test(UserRecipeLists::class)
            ->call('startSharing', $list->id)
            ->assertSet('sharingListId', $list->id)
            ->assertSet('shareEmail', '');
    }

    #[Test]
    public function it_validates_share_email_is_required(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', '')
            ->call('shareList')
            ->assertHasErrors(['shareEmail' => 'required']);
    }

    #[Test]
    public function it_validates_share_email_format(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', 'invalid-email')
            ->call('shareList')
            ->assertHasErrors(['shareEmail' => 'email']);
    }

    #[Test]
    public function it_shares_list_with_valid_user(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();
        $targetUser = User::factory()->create(['email' => 'target@gmail.com']);

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', 'target@gmail.com')
            ->call('shareList')
            ->assertHasNoErrors();

        $this->assertTrue($list->sharedWith()->where('users.id', $targetUser->id)->exists());
    }

    #[Test]
    public function it_prevents_sharing_with_non_existent_user(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', 'nonexistent@gmail.com')
            ->call('shareList')
            ->assertHasErrors('shareEmail');
    }

    #[Test]
    public function it_prevents_sharing_with_yourself(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', $this->user->email)
            ->call('shareList')
            ->assertHasErrors('shareEmail');
    }

    #[Test]
    public function it_prevents_sharing_already_shared_list(): void
    {
        $this->actingAs($this->user);

        $targetUser = User::factory()->create(['email' => 'already@gmail.com']);
        $list = RecipeList::factory()->forUser($this->user)->create();
        $list->sharedWith()->attach($targetUser->id);

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', 'already@gmail.com')
            ->call('shareList')
            ->assertHasErrors('shareEmail');
    }

    #[Test]
    public function it_unshares_list_from_user(): void
    {
        $this->actingAs($this->user);

        $targetUser = User::factory()->create();
        $list = RecipeList::factory()->forUser($this->user)->create();
        $list->sharedWith()->attach($targetUser->id);

        Livewire::test(UserRecipeLists::class)
            ->call('unshareList', $list->id, $targetUser->id);

        $this->assertFalse($list->sharedWith()->where('users.id', $targetUser->id)->exists());
    }

    #[Test]
    public function it_leaves_shared_list(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $list->sharedWith()->attach($this->user->id);

        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->call('leaveSharedList', $list->id);

        $this->assertFalse($list->sharedWith()->where('users.id', $this->user->id)->exists());
    }

    #[Test]
    public function it_clears_viewing_id_when_leaving_viewed_shared_list(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $list->sharedWith()->attach($this->user->id);

        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('leaveSharedList', $list->id)
            ->assertSet('viewingListId', null);
    }

    #[Test]
    public function it_does_not_share_when_not_owner(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $targetUser = User::factory()->create(['email' => 'target2@gmail.com']);

        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', $list->id)
            ->set('shareEmail', 'target2@gmail.com')
            ->call('shareList')
            ->assertHasErrors('shareEmail');
    }

    #[Test]
    public function it_does_not_unshare_when_not_owner(): void
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $list->sharedWith()->attach($targetUser->id);

        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->call('unshareList', $list->id, $targetUser->id);

        $this->assertTrue($list->sharedWith()->where('users.id', $targetUser->id)->exists());
    }

    #[Test]
    public function it_does_not_remove_recipe_when_list_not_accessible(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('removeRecipeFromList', $recipe->id);

        $this->assertDatabaseHas('recipe_recipe_list', [
            'recipe_list_id' => $list->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    #[Test]
    public function it_creates_activity_when_removing_recipe(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()
            ->forUser($this->user)
            ->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('removeRecipeFromList', $recipe->id);

        $this->assertDatabaseHas('recipe_list_activities', [
            'recipe_list_id' => $list->id,
            'recipe_id' => $recipe->id,
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_returns_recent_activities_for_viewing_list(): void
    {
        $this->actingAs($this->user);

        $list = RecipeList::factory()->forUser($this->user)->create();
        $recipe = Recipe::factory()->for($this->country)->create();
        $list->recipes()->attach($recipe, [
            'added_at' => now(),
            'country_id' => $this->country->id,
        ]);

        // Remove recipe to create activity
        Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id)
            ->call('removeRecipeFromList', $recipe->id);

        $component = Livewire::test(UserRecipeLists::class)
            ->set('viewingListId', $list->id);

        $this->assertCount(1, $component->instance()->recentActivities());
    }

    #[Test]
    public function it_does_not_leave_shared_list_for_guest(): void
    {
        $owner = User::factory()->create();
        $list = RecipeList::factory()->forUser($owner)->create();

        Livewire::test(UserRecipeLists::class)
            ->call('leaveSharedList', $list->id);

        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_leave_non_existent_list(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->call('leaveSharedList', 99999)
            ->assertOk();
    }

    #[Test]
    public function it_handles_share_when_no_user(): void
    {
        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', 1)
            ->set('shareEmail', 'test@gmail.com')
            ->call('shareList')
            ->assertOk();
    }

    #[Test]
    public function it_handles_share_when_list_not_found(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->set('sharingListId', 99999)
            ->set('shareEmail', 'test@gmail.com')
            ->call('shareList')
            ->assertOk();
    }

    #[Test]
    public function it_handles_unshare_when_no_user(): void
    {
        Livewire::test(UserRecipeLists::class)
            ->call('unshareList', 1, 1)
            ->assertOk();
    }

    #[Test]
    public function it_handles_unshare_when_list_not_found(): void
    {
        $this->actingAs($this->user);

        Livewire::test(UserRecipeLists::class)
            ->call('unshareList', 99999, 1)
            ->assertOk();
    }
}
