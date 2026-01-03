{{-- Country Stats --}}
<flux:card>
  <flux:heading size="lg">Statistics by Country</flux:heading>
  <flux:table class="mt-section">
    <flux:table.columns>
      <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection" wire:click="sort('code')">Code</flux:table.column>
      <flux:table.column class="ui-text-subtle">Name</flux:table.column>
      <flux:table.column class="ui-text-subtle">Locales</flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'recipes_count'" :direction="$sortDirection" wire:click="sort('recipes_count')" align="end">Recipes</flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'recipes_with_pdf_count'" :direction="$sortDirection" wire:click="sort('recipes_with_pdf_count')" align="end">with PDF</flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'ingredients_count'" :direction="$sortDirection" wire:click="sort('ingredients_count')" align="end">Ingredients</flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'menus_count'" :direction="$sortDirection" wire:click="sort('menus_count')" align="end">Menus</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
      @foreach($this->countryStats as $country)
        <flux:table.row wire:key="country-{{ $country->id }}">
          <flux:table.cell>
            <span class="font-medium">{{ Str::countryFlag($country->code) }} {{ $country->code }}</span>
          </flux:table.cell>
          <flux:table.cell>{{ __('country.' . $country->code) }}</flux:table.cell>
          <flux:table.cell>
            <div class="flex flex-wrap gap-1">
              @foreach($country->locales as $locale)
                <flux:badge size="sm">{{ $locale }}</flux:badge>
              @endforeach
            </div>
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">
            {{ number_format($country->recipes_count ?? 0) }}
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">
            {{ number_format($country->recipes_with_pdf_count ?? 0) }}
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">
            {{ number_format($country->ingredients_count ?? 0) }}
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">
            {{ number_format($country->menus_count) }}
          </flux:table.cell>
        </flux:table.row>
      @endforeach
    </flux:table.rows>
  </flux:table>
</flux:card>
