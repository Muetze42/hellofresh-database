<?php

namespace App\Livewire\User;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\WithLocalizedContextTrait;
use App\Models\RecipeList;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class UserRecipeLists extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $newListName = '';

    public string $newListDescription = '';

    public ?int $editingListId = null;

    public string $editListName = '';

    public string $editListDescription = '';

    public ?int $viewingListId = null;

    /**
     * Get the user's recipe lists.
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
            ->where('country_id', $this->countryId)
            ->withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the currently viewed list.
     */
    #[Computed]
    public function viewingList(): ?RecipeList
    {
        if (! $this->viewingListId) {
            return null;
        }

        return RecipeList::query()
            ->with('recipes')
            ->find($this->viewingListId);
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
        $list->country()->associate($this->countryId);
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
        unset($this->viewingList);
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

        if (! $list instanceof RecipeList) {
            return;
        }

        $list->recipes()->detach($recipeId);

        unset($this->viewingList, $this->recipeLists);

        Flux::toastSuccess(__('Recipe removed from list.'));
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('livewire.user.user-recipe-lists');
    }
}
