<div class="space-y-section">
  <div>
    <flux:heading size="xl">Database Statistics</flux:heading>
    <flux:text class="mt-ui">Real-time statistics about the {{ config('app.name') }} database.</flux:text>
  </div>

  {{-- Global Stats --}}
  <div class="grid gap-section sm:grid-cols-2 lg:grid-cols-4">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
          <flux:icon.utensils class="size-6 text-lime-600 dark:text-lime-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Total Recipes</flux:text>
          <flux:heading size="xl">{{ number_format($this->globalStats['recipes']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
          <flux:icon.carrot class="size-6 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Ingredients</flux:text>
          <flux:heading size="xl">{{ number_format($this->globalStats['ingredients']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-sky-100 dark:bg-sky-900/30 p-3">
          <flux:icon.calendar class="size-6 text-sky-600 dark:text-sky-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Weekly Menus</flux:text>
          <flux:heading size="xl">{{ number_format($this->globalStats['menus']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
          <flux:icon.globe class="size-6 text-violet-600 dark:text-violet-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Countries</flux:text>
          <flux:heading size="xl">{{ number_format($this->globalStats['countries']) }}</flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  {{-- Additional Stats --}}
  <div class="grid gap-section sm:grid-cols-3">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-rose-100 dark:bg-rose-900/30 p-3">
          <flux:icon.tag class="size-6 text-rose-600 dark:text-rose-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Tags</flux:text>
          <flux:heading size="lg">{{ number_format($this->globalStats['tags']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
          <flux:icon.triangle-alert class="size-6 text-red-600 dark:text-red-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Allergens</flux:text>
          <flux:heading size="lg">{{ number_format($this->globalStats['allergens']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-teal-100 dark:bg-teal-900/30 p-3">
          <flux:icon.chef-hat class="size-6 text-teal-600 dark:text-teal-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Cuisines</flux:text>
          <flux:heading size="lg">{{ number_format($this->globalStats['cuisines']) }}</flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  <div class="grid gap-section lg:grid-cols-2">
    {{-- Country Stats --}}
    <flux:card>
      <flux:heading size="lg">Statistics by Country</flux:heading>
      <flux:table class="mt-section">
        <flux:table.columns>
          <flux:table.column>Country</flux:table.column>
          <flux:table.column class="text-right">Recipes</flux:table.column>
          <flux:table.column class="text-right">with PDF</flux:table.column>
          <flux:table.column class="text-right">Ingredients</flux:table.column>
          <flux:table.column class="text-right">Menus</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
          @foreach($this->countryStats as $country)
            <flux:table.row wire:key="country-{{ $country->id }}">
              <flux:table.cell>
                <span class="font-medium">{{ $country->code }}</span>
              </flux:table.cell>
              <flux:table.cell class="text-right tabular-nums">
                {{ number_format($country->recipes_count ?? 0) }}
              </flux:table.cell>
              <flux:table.cell class="text-right tabular-nums">
                {{ number_format($country->recipes_with_pdf_count ?? 0) }}
              </flux:table.cell>
              <flux:table.cell class="text-right tabular-nums">
                {{ number_format($country->ingredients_count ?? 0) }}
              </flux:table.cell>
              <flux:table.cell class="text-right tabular-nums">
                {{ number_format($country->menus_count) }}
              </flux:table.cell>
            </flux:table.row>
          @endforeach
        </flux:table.rows>
      </flux:table>
    </flux:card>

    {{-- Difficulty Distribution --}}
    <flux:card>
      <flux:heading size="lg">Recipe Difficulty Distribution</flux:heading>
      <div class="mt-section space-y-4">
        @php
          $totalRecipes = array_sum(array_column($this->difficultyDistribution, 'count'));
          $difficultyLabels = [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'];
          $difficultyColors = [1 => 'bg-green-500', 2 => 'bg-amber-500', 3 => 'bg-red-500'];
        @endphp
        @foreach($this->difficultyDistribution as $item)
          @php
            $percentage = $totalRecipes > 0 ? ($item['count'] / $totalRecipes) * 100 : 0;
          @endphp
          <div wire:key="difficulty-{{ $item['difficulty'] }}">
            <div class="flex justify-between mb-1">
              <flux:text class="text-sm font-medium">
                {{ $difficultyLabels[$item['difficulty']] ?? 'Level ' . $item['difficulty'] }}
              </flux:text>
              <flux:text class="text-sm text-zinc-500">
                {{ number_format($item['count']) }} ({{ number_format($percentage, 1) }}%)
              </flux:text>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
              <div
                class="{{ $difficultyColors[$item['difficulty']] ?? 'bg-zinc-500' }} h-2 rounded-full transition-all"
                style="width: {{ $percentage }}%"
              ></div>
            </div>
          </div>
        @endforeach
      </div>
    </flux:card>
  </div>

  {{-- Newest Recipes --}}
  <flux:card>
    <flux:heading size="lg">Recently Added Recipes</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column>Recipe</flux:table.column>
        <flux:table.column>Country</flux:table.column>
        <flux:table.column>Difficulty</flux:table.column>
        <flux:table.column>Added</flux:table.column>
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
                <span class="text-sm">{{ $recipe->difficulty }}/3</span>
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

  <flux:text class="text-sm text-zinc-500 text-center">
    Statistics are cached and refresh every hour.
  </flux:text>
</div>
