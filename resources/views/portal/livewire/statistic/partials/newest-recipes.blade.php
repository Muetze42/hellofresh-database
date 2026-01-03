{{-- Newest Recipes --}}
<flux:card>
  <flux:heading size="lg">Recently Added Recipes</flux:heading>
  <flux:table class="mt-section">
    <flux:table.columns>
      <flux:table.column class="ui-text-subtle">Recipe</flux:table.column>
      <flux:table.column class="ui-text-subtle">Country</flux:table.column>
      <flux:table.column class="ui-text-subtle">Difficulty</flux:table.column>
      <flux:table.column class="ui-text-subtle">Added</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
      @foreach($this->newestRecipes as $recipe)
        <flux:table.row wire:key="recipe-{{ $recipe->id }}">
          <flux:table.cell class="font-medium">
            {{ $recipe->name }}
          </flux:table.cell>
          <flux:table.cell>
            <flux:badge size="sm">{{ $recipe->country->code }}</flux:badge>
          </flux:table.cell>
          <flux:table.cell>
            @if($recipe->difficulty)
              <span class="text-sm">{{ [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'][$recipe->difficulty] ?? '-' }}</span>
            @else
              <span class="text-zinc-400">-</span>
            @endif
          </flux:table.cell>
          <flux:table.cell class="text-zinc-500">
            {{ $recipe->created_at->diffForHumans() }}
          </flux:table.cell>
        </flux:table.row>
      @endforeach
    </flux:table.rows>
  </flux:table>
</flux:card>
