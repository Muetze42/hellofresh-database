<?php

namespace App\Livewire\Portal;

use App\Models\RecipeList;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class RecipeLists extends Component
{
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
            ->withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        return view('portal::livewire.recipe-lists.index')
            ->title(__('My Recipe Lists'));
    }
}
