@props(['recipes'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-section">
  @foreach ($recipes as $recipe)
    <x-recipes.recipe-card :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::Grid" wire:key="recipe-grid-{{ $recipe->id }}" />
  @endforeach
</div>
