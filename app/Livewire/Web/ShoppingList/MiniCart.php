<?php

declare(strict_types=1);

namespace App\Livewire\Web\ShoppingList;

use App\Models\Recipe;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MiniCart extends Component
{
    public string $shoppingListUrl = '';

    /** @var list<int> */
    public array $recipeIds = [];

    public function mount(): void
    {
        $this->shoppingListUrl = localized_route('localized.shopping-list.index');
    }

    /** @param list<int|string> $ids */
    #[On('mini-cart-open')]
    public function open(array $ids): void
    {
        $this->recipeIds = array_map(intval(...), $ids);
        Flux::showModal('mini-cart-modal');
    }

    /** @return Collection<int, Recipe> */
    #[Computed]
    public function recipes(): Collection
    {
        if ($this->recipeIds === []) {
            return collect();
        }

        return Recipe::whereIn('id', $this->recipeIds)
            ->get(['id', 'name'])
            ->keyBy('id');
    }

    public function render(): View
    {
        return view('livewire.shopping-list.mini-cart');
    }
}
