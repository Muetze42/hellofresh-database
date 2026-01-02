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

        <x-slot name="trailing">
          {{ $prepTimeRange[0] }}
          &ndash;
          {{ $prepTimeRange[1] }} {{ __('min') }}
        </x-slot>
      </flux:label>

      <flux:slider
        wire:model.live="prepTimeRange"
        range
        step="10"
        :min="0"
        :max="ceil($this->country()->prep_max / 10) * 10"
      />
    </flux:field>
  @endif

  @if ($this->country()->total_min !== null && $this->country()->total_max !== null)
    <flux:field>
      <flux:label>
        {{ __('Total Time') }}

        <x-slot name="trailing">
          {{ $totalTimeRange[0] }}
          &ndash;
          {{ $totalTimeRange[1] }} {{ __('min') }}
        </x-slot>
      </flux:label>

      <flux:slider
        wire:model.live="totalTimeRange"
        range
        step="10"
        :min="0"
        :max="ceil($this->country()->total_max / 10) * 10"
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
