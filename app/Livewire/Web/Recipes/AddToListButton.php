<?php

namespace App\Livewire\Web\Recipes;

use App\Enums\RecipeListActionEnum;
use App\Events\RecipeListUpdatedEvent;
use App\Livewire\Web\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\RecipeList;
use App\Models\RecipeListActivity;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class AddToListButton extends AbstractComponent
{
    use WithLocalizedContextTrait;

    #[Locked]
    public int $recipeId;

    public bool $isModalOpen = false;

    public string $search = '';

    /** @var array<int, int> */
    public array $selectedLists = [];

    /**
     * Get the event listeners for this component.
     *
     * @return array<string, string>
     */
    protected function getListeners(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [
                'user-authenticated' => 'refreshLists',
            ];
        }

        return [
            'user-authenticated' => 'refreshLists',
            sprintf('echo-private:App.Models.User.%s,.RecipeListUpdated', $user->id) => 'handleRecipeListUpdated',
        ];
    }

    /**
     * Handle recipe list updates from other tabs/windows.
     *
     * @param  array{recipeId: int, countryId: int}  $event
     */
    public function handleRecipeListUpdated(array $event): void
    {
        if ($event['recipeId'] !== $this->recipeId) {
            return;
        }

        if ($event['countryId'] !== $this->countryId) {
            return;
        }

        $this->loadSelectedLists();
        unset($this->lists, $this->isInAnyList);
    }

    /**
     * Called when the component is mounted.
     */
    public function mount(int $recipeId): void
    {
        $this->recipeId = $recipeId;
        $this->loadSelectedLists();
    }

    /**
     * Load the currently selected lists for this recipe.
     */
    protected function loadSelectedLists(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->selectedLists = [];

            return;
        }

        $this->selectedLists = RecipeList::where('country_id', $this->countryId)
            ->where(fn (Builder $query): Builder => $query
                ->where('user_id', $user->id)
                ->orWhereHas('sharedWith', fn (Builder $sub): Builder => $sub->where('users.id', $user->id))
            )
            ->whereHas('recipes', fn (Builder $query): Builder => $query->where('recipe_id', $this->recipeId))
            ->pluck('id')
            ->all();
    }

    /**
     * Get the user's recipe lists (owned and shared).
     *
     * @return Collection<int, RecipeList>
     */
    #[Computed]
    public function lists(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return RecipeList::where('country_id', $this->countryId)
            ->where(fn (Builder $query): Builder => $query
                ->where('user_id', $user->id)
                ->orWhereHas('sharedWith', fn (Builder $sub): Builder => $sub->where('users.id', $user->id))
            )
            ->with('user')
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if the recipe is in any list.
     */
    #[Computed]
    public function isInAnyList(): bool
    {
        return $this->selectedLists !== [];
    }

    /**
     * Save the selected lists for this recipe.
     */
    public function saveLists(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $currentLists = RecipeList::where('country_id', $this->countryId)
            ->where(fn (Builder $query): Builder => $query
                ->where('user_id', $user->id)
                ->orWhereHas('sharedWith', fn (Builder $sub): Builder => $sub->where('users.id', $user->id))
            )
            ->whereHas('recipes', fn (Builder $query): Builder => $query->where('recipe_id', $this->recipeId))
            ->pluck('id')
            ->all();

        $toAdd = array_diff($this->selectedLists, $currentLists);
        $toRemove = array_diff($currentLists, $this->selectedLists);

        foreach ($toAdd as $listId) {
            /** @var RecipeList|null $list */
            $list = RecipeList::find($listId);
            if ($list instanceof RecipeList && $list->isAccessibleBy($user)) {
                $list->recipes()->attach($this->recipeId, ['added_at' => now()]);
                $this->logActivity($list, RecipeListActionEnum::Added);
            }
        }

        foreach ($toRemove as $listId) {
            /** @var RecipeList|null $list */
            $list = RecipeList::find($listId);
            if ($list instanceof RecipeList && $list->isAccessibleBy($user)) {
                $list->recipes()->detach($this->recipeId);
                $this->logActivity($list, RecipeListActionEnum::Removed);
            }
        }

        unset($this->lists, $this->isInAnyList);

        $this->isModalOpen = false;
        $this->search = '';
        Flux::closeModal('add-to-list-' . $this->recipeId);

        if ($toAdd !== [] || $toRemove !== []) {
            Flux::toastSuccess(__('List updated.'));

            RecipeListUpdatedEvent::dispatch($user->id, $this->recipeId, $this->countryId);
        }
    }

    /**
     * Create a new list and add the recipe.
     */
    public function createList(): void
    {
        $listName = trim($this->search);

        if (strlen($listName) < 2 || strlen($listName) > 255) {
            return;
        }

        $user = auth()->user();

        if (! $user) {
            $this->dispatch('require-auth');

            return;
        }

        $list = new RecipeList([
            'name' => $listName,
        ]);

        $list->user()->associate($user);
        $list->country()->associate($this->countryId);
        $list->save();

        $list->recipes()->attach($this->recipeId, ['added_at' => now()]);
        $this->logActivity($list, RecipeListActionEnum::Added);

        $this->selectedLists[] = $list->id;
        $this->search = '';

        unset($this->lists, $this->isInAnyList);

        Flux::toastSuccess(__('List created and recipe added.'));

        RecipeListUpdatedEvent::dispatch($user->id, $this->recipeId, $this->countryId);
    }

    /**
     * Open the list modal.
     */
    public function openModal(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->dispatch('require-auth');

            return;
        }

        $this->loadSelectedLists();
        unset($this->lists, $this->isInAnyList);

        $this->isModalOpen = true;
        Flux::showModal('add-to-list-' . $this->recipeId);
    }

    /**
     * Close the list modal.
     */
    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->search = '';
    }

    /**
     * Refresh when user authenticates.
     */
    public function refreshLists(): void
    {
        $this->loadSelectedLists();
        unset($this->lists, $this->isInAnyList);
    }

    /**
     * Log an activity for a recipe list.
     */
    protected function logActivity(RecipeList $list, RecipeListActionEnum $action): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $activity = new RecipeListActivity(['action' => $action]);
        $activity->recipeList()->associate($list);
        $activity->user()->associate($user);
        $activity->recipe()->associate($this->recipeId);
        $activity->save();
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.recipes.add-to-list-button');
    }
}
