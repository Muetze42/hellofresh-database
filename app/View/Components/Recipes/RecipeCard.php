<?php

namespace App\View\Components\Recipes;

use App\Enums\ViewModeEnum;
use App\Models\Recipe;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecipeCard extends Component
{
    /**
     * Create a new component instance.
     *
     * @param  array<int>  $tagIds
     */
    public function __construct(
        public Recipe $recipe,
        public ViewModeEnum $viewMode = ViewModeEnum::Grid,
        public array $tagIds = [],
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('web::components.recipes.recipe-card');
    }
}
