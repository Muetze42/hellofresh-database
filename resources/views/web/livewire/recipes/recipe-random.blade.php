<flux:main class="space-y-section" container>
  <div class="flex flex-col gap-ui sm:flex-row sm:items-center sm:justify-between">
    <flux:heading size="xl" class="hidden sm:block">{{ __('Random Recipes') }}</flux:heading>

    {{-- Desktop: Controls --}}
    <div class="hidden sm:flex items-center gap-4">
      <flux:button wire:click="shuffle" icon="shuffle" size="sm">
        {{ __('Shuffle') }}
      </flux:button>

      <flux:modal.trigger name="recipe-filters">
        <flux:button icon="sliders-horizontal" size="sm">
          {{ __('Filter') }}
          @if ($this->activeFilterCount > 0)
            <flux:badge size="sm" color="lime" inset="right">{{ $this->activeFilterCount }}</flux:badge>
          @endif
        </flux:button>
      </flux:modal.trigger>

      <flux:tabs wire:model.live="viewMode" variant="segmented" size="sm">
        <flux:tab :name="\App\Enums\ViewModeEnum::Grid->value" icon="squares-2x2">{{ __('Grid') }}</flux:tab>
        <flux:tab :name="\App\Enums\ViewModeEnum::List->value" icon="list-bullet">{{ __('List') }}</flux:tab>
      </flux:tabs>
    </div>
  </div>

  {{-- Mobile: Shuffle left, Filter+View right --}}
  <div class="flex items-center gap-ui sm:hidden">
    <flux:button wire:click="shuffle" icon="shuffle" size="sm" class="flex-1">
      {{ __('Shuffle') }}
    </flux:button>

    <div class="flex items-center gap-ui shrink-0">
      <flux:modal.trigger name="recipe-filters">
        <flux:button size="sm">
          <flux:icon.sliders-horizontal class="size-5" />
          @if ($this->activeFilterCount > 0)
            <flux:badge size="sm" color="lime">{{ $this->activeFilterCount }}</flux:badge>
          @endif
        </flux:button>
      </flux:modal.trigger>

      <flux:tabs wire:model.live="viewMode" variant="segmented" size="sm">
        <flux:tab :name="\App\Enums\ViewModeEnum::Grid->value" icon="squares-2x2" />
        <flux:tab :name="\App\Enums\ViewModeEnum::List->value" icon="list-bullet" />
      </flux:tabs>
    </div>
  </div>

  @include('web::partials.recipes.filter-modal')

  @if ($this->randomRecipes->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
      <flux:icon.search-x class="size-12 text-zinc-400" />
      <flux:heading size="lg" class="mt-4">{{ __('No recipes found') }}</flux:heading>
      <flux:text class="mt-2 text-zinc-500">{{ __('No recipes match your current filter settings.') }}</flux:text>

      @if ($this->activeFilterCount > 0)
        <flux:button wire:click="clearFilters" variant="primary" class="mt-section">
          {{ __('Clear All Filters') }}
        </flux:button>
      @endif
    </div>
  @else
    @if ($viewMode === \App\Enums\ViewModeEnum::Grid->value)
      <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($this->randomRecipes as $recipe)
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::Grid" />
        @endforeach
      </div>
    @else
      <div class="flex flex-col gap-4">
        @foreach ($this->randomRecipes as $recipe)
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::List" />
        @endforeach
      </div>
    @endif
  @endif
</flux:main>
