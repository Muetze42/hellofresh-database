<flux:main class="space-y-section" container>
  <div class="flex flex-col gap-ui sm:flex-row sm:items-center sm:justify-between">
    {{-- Desktop: Title + Week selector --}}
    @if ($this->menuData)
      <div class="hidden sm:flex items-center gap-4">
        <flux:heading size="xl">{{ __('Menu') }}</flux:heading>

        <flux:select wire:model.live="selectedMenuWeek" variant="listbox" size="sm">
          @foreach ($this->menuData['list'] as $menuItem)
            <flux:select.option wire:key="menu-desktop-{{ $menuItem['value'] }}" :value="$menuItem['value']">
              {{ $menuItem['start'] }} - {{ $menuItem['end'] }} {{ $menuItem['year'] }}
            </flux:select.option>
          @endforeach
        </flux:select>
      </div>
    @else
      <flux:heading size="xl" class="hidden sm:block">{{ __('Recipes') }}</flux:heading>
    @endif

    {{-- Desktop: Controls --}}
    <div class="hidden sm:flex items-center gap-4">
      <div class="w-64 shrink-0">
        <flux:input
          wire:model.live.debounce.300ms="search"
          icon="search"
          :placeholder="__('Search...')"
          size="sm"
          clearable
        />
      </div>
      <flux:select wire:model.live="sortBy" variant="listbox" size="sm" class="min-w-48">
        @foreach (\App\Enums\RecipeSortEnum::cases() as $sortOption)
          <flux:select.option wire:key="sort-desktop-{{ $sortOption->value }}" :value="$sortOption->value">
            {{ $sortOption->label() }}
          </flux:select.option>
        @endforeach
      </flux:select>

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

  {{-- Mobile: Search --}}
  <div class="sm:hidden">
    <flux:input
      wire:model.live.debounce.300ms="search"
      icon="search"
      :placeholder="__('Search...')"
      size="sm"
      clearable
    />
  </div>

  {{-- Mobile: Sort left, Filter+View right --}}
  <div class="flex items-center gap-ui sm:hidden">
    <flux:select wire:model.live="sortBy" variant="listbox" size="sm" class="min-w-0 flex-1">
      @foreach (\App\Enums\RecipeSortEnum::cases() as $sortOption)
        <flux:select.option wire:key="sort-mobile-{{ $sortOption->value }}" :value="$sortOption->value">
          {{ $sortOption->label() }}
        </flux:select.option>
      @endforeach
    </flux:select>

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

  {{-- Mobile Menu: Week selector in second row --}}
  @if ($this->menuData)
    <div class="sm:hidden">
      <flux:select wire:model.live="selectedMenuWeek" variant="listbox" size="sm" class="w-full">
        @foreach ($this->menuData['list'] as $menuItem)
          <flux:select.option wire:key="menu-mobile-{{ $menuItem['value'] }}" :value="$menuItem['value']">
            {{ $menuItem['start'] }} - {{ $menuItem['end'] }} · {{ $menuItem['year'] }}
          </flux:select.option>
        @endforeach
      </flux:select>
    </div>
  @endif

  @include('web::partials.recipes.filter-modal')

  @if ($this->recipes->isEmpty())
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
        @foreach ($this->recipes as $recipe)
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::Grid" :tag-ids="$tagIds" />
        @endforeach
      </div>
    @else
      <div class="flex flex-col gap-4">
        @foreach ($this->recipes as $recipe)
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::List" :tag-ids="$tagIds" />
        @endforeach
      </div>
    @endif

    <flux:pagination :paginator="$this->recipes" />
  @endif
</flux:main>
