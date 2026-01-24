<?php

namespace App\Livewire\Web\User;

use App\Enums\RecipeListActionEnum;
use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\RecipeList;
use App\Models\RecipeListActivity;
use App\Models\User;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('web::components.layouts.localized')]
class UserRecipeLists extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $newListName = '';

    public string $newListDescription = '';

    public ?int $editingListId = null;

    public string $editListName = '';

    public string $editListDescription = '';

    public ?int $viewingListId = null;

    public ?int $sharingListId = null;

    public string $shareEmail = '';

    /**
     * Get the user's own recipe lists.
     *
     * @return Collection<int, RecipeList>
     */
    #[Computed]
    public function recipeLists(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return RecipeList::where('user_id', $user->id)
            ->with('sharedWith')
            ->withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get recipe lists shared with the user.
     *
     * @return Collection<int, RecipeList>
     */
    #[Computed]
    public function sharedLists(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return RecipeList::whereHas('sharedWith', fn (Builder $query): Builder => $query->where('users.id', $user->id))
            ->with('user')
            ->withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the currently viewed list with recipes filtered by current country.
     */
    #[Computed]
    public function viewingList(): ?RecipeList
    {
        if (! $this->viewingListId) {
            return null;
        }

        return RecipeList::with([
            'recipes' => fn (BelongsToMany $query): BelongsToMany => $query->wherePivot('country_id', $this->countryId),
            'user',
            'sharedWith',
        ])
            ->find($this->viewingListId);
    }

    /**
     * Get the count of recipes from other countries in the currently viewed list.
     */
    #[Computed]
    public function otherCountriesRecipeCount(): int
    {
        if (! $this->viewingListId) {
            return 0;
        }

        return RecipeList::find($this->viewingListId)
            ?->recipes()
            ->wherePivot('country_id', '!=', $this->countryId)
            ->count() ?? 0;
    }

    /**
     * Get recent activities for the currently viewed list.
     *
     * @return Collection<int, RecipeListActivity>
     */
    #[Computed]
    public function recentActivities(): Collection
    {
        if (! $this->viewingListId) {
            return collect();
        }

        return RecipeListActivity::where('recipe_list_id', $this->viewingListId)
            ->with(['user', 'recipe.country'])->latest()
            ->limit(10)
            ->get();
    }

    /**
     * Create a new recipe list.
     */
    public function createList(): void
    {
        $this->validate([
            'newListName' => ['required', 'string', 'min:2', 'max:255'],
            'newListDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $list = new RecipeList([
            'name' => $this->newListName,
            'description' => $this->newListDescription,
        ]);

        $list->user()->associate($user);
        $list->save();

        $this->reset(['newListName', 'newListDescription']);
        unset($this->recipeLists);

        Flux::closeModal('create-list');
        Flux::toastSuccess(__('List created successfully.'));
    }

    /**
     * Start editing a list.
     */
    public function startEditing(int $listId): void
    {
        $list = RecipeList::find($listId);

        if (! $list) {
            return;
        }

        $this->editingListId = $listId;
        $this->editListName = $list->name;
        $this->editListDescription = $list->description ?? '';

        Flux::showModal('edit-list');
    }

    /**
     * Update a recipe list.
     */
    public function updateList(): void
    {
        $this->validate([
            'editListName' => ['required', 'string', 'min:2', 'max:255'],
            'editListDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        $list = RecipeList::find($this->editingListId);

        if (! $list) {
            return;
        }

        $list->update([
            'name' => $this->editListName,
            'description' => $this->editListDescription,
        ]);

        $this->reset(['editingListId', 'editListName', 'editListDescription']);
        unset($this->recipeLists);

        Flux::closeModal('edit-list');
        Flux::toastSuccess(__('List updated successfully.'));
    }

    /**
     * Delete a recipe list.
     */
    public function deleteList(int $listId): void
    {
        RecipeList::destroy($listId);

        if ($this->viewingListId === $listId) {
            $this->viewingListId = null;
        }

        unset($this->recipeLists);

        Flux::toastSuccess(__('List deleted successfully.'));
    }

    /**
     * View a list's recipes.
     */
    public function viewList(int $listId): void
    {
        $this->viewingListId = $listId;
        unset($this->viewingList, $this->otherCountriesRecipeCount);
    }

    /**
     * Go back to list overview.
     */
    public function backToLists(): void
    {
        $this->viewingListId = null;
    }

    /**
     * Remove a recipe from a list.
     */
    public function removeRecipeFromList(int $recipeId): void
    {
        $list = $this->viewingList();
        $user = auth()->user();

        if (! $list instanceof RecipeList || ! $user) {
            return;
        }

        if (! $list->isAccessibleBy($user)) {
            return;
        }

        $list->recipes()->detach($recipeId);

        $activity = new RecipeListActivity(['action' => RecipeListActionEnum::Removed]);
        $activity->recipeList()->associate($list);
        $activity->user()->associate($user);
        $activity->recipe()->associate($recipeId);
        $activity->save();

        unset($this->viewingList, $this->recipeLists, $this->otherCountriesRecipeCount);

        Flux::toastSuccess(__('Recipe removed from list.'));
    }

    /**
     * Open the share modal for a list.
     */
    public function startSharing(int $listId): void
    {
        $this->sharingListId = $listId;
        $this->shareEmail = '';
        $this->resetValidation();
        Flux::showModal('share-list');
    }

    /**
     * Share a list with a user by email.
     */
    public function shareList(): void
    {
        $this->validate([
            'shareEmail' => ['required', 'email'],
        ]);

        $user = auth()->user();
        $list = RecipeList::find($this->sharingListId);

        if (! $user || ! $list instanceof RecipeList) {
            return;
        }

        if (! $list->isOwnedBy($user)) {
            $this->addError('shareEmail', __('Only the owner can share this list.'));

            return;
        }

        $targetUser = User::where('email', $this->shareEmail)->first();

        if (! $targetUser) {
            $this->addError('shareEmail', __('No user found with this email address.'));

            return;
        }

        if ($targetUser->id === $user->id) {
            $this->addError('shareEmail', __('You cannot share a list with yourself.'));

            return;
        }

        if ($list->sharedWith()->where('users.id', $targetUser->id)->exists()) {
            $this->addError('shareEmail', __('This list is already shared with this user.'));

            return;
        }

        $list->sharedWith()->attach($targetUser->id);

        $this->reset(['sharingListId', 'shareEmail']);
        unset($this->recipeLists);

        Flux::closeModal('share-list');
        Flux::toastSuccess(__('List shared successfully.'));
    }

    /**
     * Remove a user from a shared list.
     */
    public function unshareList(int $listId, int $userId): void
    {
        $user = auth()->user();
        $list = RecipeList::find($listId);

        if (! $user || ! $list instanceof RecipeList) {
            return;
        }

        if (! $list->isOwnedBy($user)) {
            return;
        }

        $list->sharedWith()->detach($userId);

        unset($this->recipeLists);

        Flux::toastSuccess(__('User removed from shared list.'));
    }

    /**
     * Leave a shared list.
     */
    public function leaveSharedList(int $listId): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $list = RecipeList::find($listId);

        if (! $list instanceof RecipeList) {
            return;
        }

        $list->sharedWith()->detach($user->id);

        if ($this->viewingListId === $listId) {
            $this->viewingListId = null;
        }

        unset($this->sharedLists);

        Flux::toastSuccess(__('You have left the shared list.'));
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.user.user-recipe-lists');
    }
}
