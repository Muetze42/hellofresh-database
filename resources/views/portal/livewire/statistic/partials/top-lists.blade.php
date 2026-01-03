{{-- Top Lists --}}
<div class="grid gap-section lg:grid-cols-3">
  <flux:card>
    <flux:heading size="lg">Top 10 Ingredients</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Ingredient</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->topIngredients as $ingredient)
          <flux:table.row wire:key="ingredient-{{ $loop->index }}">
            <flux:table.cell class="truncate max-w-48">{{ json_decode($ingredient->name)->en ?? $ingredient->name }}</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ number_format($ingredient->recipes_count) }}</flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <flux:card>
    <flux:heading size="lg">Top 10 Tags</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Tag</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->topTags as $tag)
          <flux:table.row wire:key="tag-{{ $loop->index }}">
            <flux:table.cell class="truncate max-w-48">{{ json_decode($tag->name)->en ?? $tag->name }}</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ number_format($tag->recipes_count) }}</flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <flux:card>
    <flux:heading size="lg">Top 10 Cuisines</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Cuisine</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->topCuisines as $cuisine)
          <flux:table.row wire:key="cuisine-{{ $loop->index }}">
            <flux:table.cell class="truncate max-w-48">{{ json_decode($cuisine->name)->en ?? $cuisine->name }}</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ number_format($cuisine->recipes_count) }}</flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
