<flux:main class="space-y-section" container>
  <div class="flex flex-col gap-ui sm:flex-row sm:items-center sm:justify-between">
    {{-- Desktop: Title + Week selector --}}
    @if ($this->menuData)
      <div class="hidden sm:flex items-center gap-4">
        <flux:heading size="xl">{{ __('Menu') }}</flux:heading>

        <flux:select wire:model.live="selectedMenuWeek" variant="listbox" size="sm">
          @foreach ($this->menuData['list'] as $menuItem)
            <flux:select.option wire:key="menu-desktop-{{ $menuItem['value'] }}" :value="$menuItem['value']">
              {{ $menuItem['start'] }} - {{ $menuItem['end'] }}
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
            {{ $menuItem['start'] }} - {{ $menuItem['end'] }}
          </flux:select.option>
        @endforeach
      </flux:select>
    </div>
  @endif

  <flux:modal name="recipe-filters" variant="flyout" class="space-y-section w-84">
    <div>
      <flux:heading size="lg">{{ __('Filter Recipes') }}</flux:heading>
    </div>

    <flux:switch wire:model.live="filterHasPdf" :label="__('Only with PDF')" />

    <flux:field>
      <flux:label>{{ __('Difficulty') }}</flux:label>

      <flux:checkbox.group wire:model.live="difficultyLevels">
        @foreach (\App\Enums\DifficultyEnum::cases() as $difficulty)
          <flux:checkbox :value="$difficulty->value" :label="$difficulty->label()" />
        @endforeach
      </flux:checkbox.group>
    </flux:field>

    @if ($this->country()->prep_min !== null && $this->country()->prep_max !== null)
      <flux:field>
        <flux:label>
          {{ __('Prep Time') }}
          @if ($this->isPrepTimeFilterActive())
            <span class="text-zinc-500 font-normal">
              ({{ $prepTimeRange[0] }} - {{ $prepTimeRange[1] }} {{ __('min') }})
            </span>
          @endif
        </flux:label>

        <flux:slider
          wire:model.live="prepTimeRange"
          range
          :min="$this->country()->prep_min"
          :max="$this->country()->prep_max"
        />
      </flux:field>
    @endif

    @if ($this->country()->total_min !== null && $this->country()->total_max !== null)
      <flux:field>
        <flux:label>
          {{ __('Total Time') }}
          @if ($this->isTotalTimeFilterActive())
            <span class="text-zinc-500 font-normal">
              ({{ $totalTimeRange[0] }} - {{ $totalTimeRange[1] }} {{ __('min') }})
            </span>
          @endif
        </flux:label>

        <flux:slider
          wire:model.live="totalTimeRange"
          range
          :min="$this->country()->total_min"
          :max="$this->country()->total_max"
        />
      </flux:field>
    @endif

    <div class="space-y-3">
      <flux:field>
        <flux:label>{{ __('With Ingredients') }}</flux:label>

        <flux:radio.group wire:model.live="ingredientMatchMode" variant="segmented" size="sm">
          <flux:radio :value="\App\Enums\IngredientMatchModeEnum::Any->value" :label="__('Any of')" />
          <flux:radio :value="\App\Enums\IngredientMatchModeEnum::All->value" :label="__('All of')" />
        </flux:radio.group>
      </flux:field>

      <flux:pillbox
        wire:model.live="ingredientIds"
        variant="combobox"
        multiple
        :placeholder="__('Search ingredients...')"
      >
        <x-slot name="input">
          <flux:pillbox.input wire:model.live.debounce.300ms="ingredientSearch" :placeholder="__('Search...')" />
        </x-slot>

        @foreach ($this->selectedIngredients as $ingredient)
          <flux:pillbox.option wire:key="selected-ingredient-{{ $ingredient->id }}" :value="$ingredient->id" selected>
            {{ $ingredient->name }}
          </flux:pillbox.option>
        @endforeach

        @foreach ($this->ingredientResults as $ingredient)
          <flux:pillbox.option wire:key="ingredient-{{ $ingredient->id }}" :value="$ingredient->id">
            {{ $ingredient->name }}
          </flux:pillbox.option>
        @endforeach

        <x-slot name="empty">
          <flux:pillbox.option.empty>
            {{ __('No ingredients found.') }}
          </flux:pillbox.option.empty>
        </x-slot>
      </flux:pillbox>
    </div>

    <flux:pillbox
      wire:model.live="excludedIngredientIds"
      variant="combobox"
      multiple
      :label="__('Without Ingredients')"
      :placeholder="__('Search ingredients...')"
    >
      <x-slot name="input">
        <flux:pillbox.input wire:model.live.debounce.300ms="excludedIngredientSearch" :placeholder="__('Search...')" />
      </x-slot>

      @foreach ($this->selectedExcludedIngredients as $ingredient)
        <flux:pillbox.option wire:key="selected-excluded-ingredient-{{ $ingredient->id }}" :value="$ingredient->id" selected>
          {{ $ingredient->name }}
        </flux:pillbox.option>
      @endforeach

      @foreach ($this->excludedIngredientResults as $ingredient)
        <flux:pillbox.option wire:key="excluded-ingredient-{{ $ingredient->id }}" :value="$ingredient->id">
          {{ $ingredient->name }}
        </flux:pillbox.option>
      @endforeach

      <x-slot name="empty">
        <flux:pillbox.option.empty>
          {{ __('No ingredients found.') }}
        </flux:pillbox.option.empty>
      </x-slot>
    </flux:pillbox>

    @if ($this->tags->isNotEmpty())
      <flux:pillbox
        wire:model.live="tagIds"
        multiple
        searchable
        :label="__('With Tags')"
        placeholder="{{ __('Select tags...') }}"
      >
        @foreach ($this->tags as $tag)
          <flux:pillbox.option wire:key="tag-{{ $tag->id }}" :value="$tag->id">
            {{ $tag->name }}
          </flux:pillbox.option>
        @endforeach
      </flux:pillbox>

      <flux:pillbox
        wire:model.live="excludedTagIds"
        multiple
        searchable
        :label="__('Without Tags')"
        placeholder="{{ __('Select tags...') }}"
      >
        @foreach ($this->tags as $tag)
          <flux:pillbox.option wire:key="excluded-tag-{{ $tag->id }}" :value="$tag->id">
            {{ $tag->name }}
          </flux:pillbox.option>
        @endforeach
      </flux:pillbox>
    @endif

    @if ($this->labels->isNotEmpty())
      <flux:pillbox
        wire:model.live="labelIds"
        multiple
        searchable
        :label="__('With Labels')"
        placeholder="{{ __('Select labels...') }}"
      >
        @foreach ($this->labels as $label)
          <flux:pillbox.option wire:key="label-{{ $label->id }}" :value="$label->id">
            {{ $label->name }}
          </flux:pillbox.option>
        @endforeach
      </flux:pillbox>

      <flux:pillbox
        wire:model.live="excludedLabelIds"
        multiple
        searchable
        :label="__('Without Labels')"
        placeholder="{{ __('Select labels...') }}"
      >
        @foreach ($this->labels as $label)
          <flux:pillbox.option wire:key="excluded-label-{{ $label->id }}" :value="$label->id">
            {{ $label->name }}
          </flux:pillbox.option>
        @endforeach
      </flux:pillbox>
    @endif

    @if ($this->allergens->isNotEmpty())
      <flux:pillbox
        wire:model.live="excludedAllergenIds"
        multiple
        searchable
        :label="__('Exclude Allergens')"
        placeholder="{{ __('Select allergens...') }}"
      >
        @foreach ($this->allergens as $allergen)
          <flux:pillbox.option wire:key="allergen-{{ $allergen->id }}" :value="$allergen->id">
            {{ $allergen->name }}
          </flux:pillbox.option>
        @endforeach
      </flux:pillbox>
    @endif

    @if ($this->activeFilterCount > 0)
      <flux:button wire:click="clearFilters" variant="danger" class="w-full">
        {{ __('Clear All Filters') }}
      </flux:button>
    @endif
  </flux:modal>

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
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::Grid" />
        @endforeach
      </div>
    @else
      <div class="flex flex-col gap-4">
        @foreach ($this->recipes as $recipe)
          <x-recipes.recipe-card wire:key="recipe-{{ $recipe->id }}" :recipe="$recipe" :view-mode="\App\Enums\ViewModeEnum::List" />
        @endforeach
      </div>
    @endif

    <flux:pagination :paginator="$this->recipes" />
  @endif
</flux:main>
