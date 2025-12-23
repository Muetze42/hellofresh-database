<?php

namespace App\Livewire\User;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\WithLocalizedContextTrait;
use App\Models\ShoppingList;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class UserShoppingLists extends AbstractComponent
{
    use WithLocalizedContextTrait;

    /**
     * Get the user's shopping lists.
     *
     * @return Collection<int, ShoppingList>
     */
    #[Computed]
    public function shoppingLists(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return ShoppingList::where('user_id', $user->id)
            ->where('country_id', $this->countryId)
            ->latest()
            ->get();
    }

    /**
     * Load a shopping list into the current shopping list.
     *
     * @param  array{recipe_id: int, servings: int}[]  $items
     */
    public function loadList(array $items): void
    {
        $this->dispatch('load-shopping-list', items: $items);

        Flux::toastSuccess(__('Shopping list loaded.'));

        $this->redirect(localized_route('localized.shopping-list.index'), navigate: true);
    }

    /**
     * Delete a shopping list.
     */
    public function deleteList(int $listId): void
    {
        ShoppingList::destroy($listId);

        unset($this->shoppingLists);

        Flux::toastSuccess(__('Shopping list deleted.'));
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('livewire.user.user-shopping-lists');
    }
}
