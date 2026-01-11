<flux:main container class="space-y-section">
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item :href="route('portal.stats.index')" wire:navigate>Statistics</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Recipes</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div>
    <flux:heading size="xl">Recipe Statistics</flux:heading>
    <flux:text class="mt-ui">Statistics about recipes in the {{ config('app.name') }} database.</flux:text>
  </div>

  {{-- Global Stats --}}
  <div class="grid gap-section sm:grid-cols-2 xl:grid-cols-4">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
          <flux:icon.utensils class="size-6 text-lime-600 dark:text-lime-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Total Recipes</flux:text>
          <flux:heading size="xl">{{ Number::format($this->globalStats['recipes']) }}</flux:heading>
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
          <flux:heading size="xl">{{ Number::format($this->globalStats['ingredients']) }}</flux:heading>
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
          <flux:heading size="xl">{{ Number::format($this->globalStats['menus']) }}</flux:heading>
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
          <flux:heading size="xl">{{ Number::format($this->globalStats['countries']) }}</flux:heading>
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
          <flux:heading size="lg">{{ Number::format($this->globalStats['tags']) }}</flux:heading>
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
          <flux:heading size="lg">{{ Number::format($this->globalStats['allergens']) }}</flux:heading>
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
          <flux:heading size="lg">{{ Number::format($this->globalStats['cuisines']) }}</flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  {{-- Recipe Quality & Data Health --}}
  <div class="grid gap-section lg:grid-cols-2">
    <flux:card>
      <flux:heading size="lg">Recipe Quality</flux:heading>
      <div class="mt-section space-y-4">
        <div class="flex justify-between items-center">
          <flux:text>Recipes with PDF</flux:text>
          <div class="text-right">
            <flux:heading size="lg" class="tabular-nums inline-flex gap-1 items-baseline">
              <flux:text class="text-sm text-zinc-500">{{ $this->recipeQuality['pdf_percentage'] }}%</flux:text>
              {{ Number::format($this->recipeQuality['with_pdf']) }}
            </flux:heading>
          </div>
        </div>
        <div class="flex justify-between items-center">
          <flux:text>Recipes without Image</flux:text>
          <flux:heading size="lg" class="tabular-nums {{ $this->recipeQuality['without_image'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
            {{ Number::format($this->recipeQuality['without_image']) }}
          </flux:heading>
        </div>
        <div class="flex justify-between items-center">
          <flux:text>Recipes without Nutrition</flux:text>
          <flux:heading size="lg" class="tabular-nums {{ $this->recipeQuality['without_nutrition'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
            {{ Number::format($this->recipeQuality['without_nutrition']) }}
          </flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <flux:heading size="lg">Data Health</flux:heading>
      <div class="mt-section space-y-4">
        <div class="flex justify-between items-center">
          <flux:text>Orphan Ingredients</flux:text>
          <flux:heading size="lg" class="tabular-nums {{ $this->dataHealth['orphan_ingredients'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}">
            {{ Number::format($this->dataHealth['orphan_ingredients']) }}
          </flux:heading>
        </div>
        <div class="flex justify-between items-center">
          <flux:text>Inactive Countries</flux:text>
          <flux:heading size="lg" class="tabular-nums">{{ Number::format($this->dataHealth['inactive_countries']) }}</flux:heading>
        </div>
        <div class="flex justify-between items-center">
          <flux:text>Recipes without Tags</flux:text>
          <flux:heading size="lg" class="tabular-nums {{ $this->dataHealth['recipes_without_tags'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
            {{ Number::format($this->dataHealth['recipes_without_tags']) }}
          </flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  {{-- Country Stats --}}
  <flux:card class="space-y-section">
    <flux:heading size="lg">Statistics by Country</flux:heading>
    <flux:table>
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
              <span class="font-medium"><x-flag :code="$country->code" /> {{ $country->code }}</span>
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
              {{ Number::format($country->recipes_count ?? 0) }}
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">
              {{ Number::format($country->recipes_with_pdf_count ?? 0) }}
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">
              {{ Number::format($country->ingredients_count ?? 0) }}
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">
              {{ Number::format($country->menus_count) }}
            </flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>

  {{-- Top Lists --}}
  <div class="grid gap-section lg:grid-cols-3">
    <flux:card class="space-y-section">
      <flux:heading size="lg">Top 10 Ingredients</flux:heading>
      <flux:table container:class="sticky-section-child">
        <flux:table.columns sticky>
          <flux:table.column class="ui-text-subtle">Ingredient</flux:table.column>
          <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
          @foreach($this->topIngredients as $ingredient)
            @php
              $locales = json_decode($ingredient->country_locales ?? '["en"]', true);
              $names = is_string($ingredient->name) ? json_decode($ingredient->name, true) : $ingredient->name;
              $ingredientName = $names[$locales[0] ?? 'en'] ?? (is_array($names) ? array_values($names)[0] ?? '' : '');
            @endphp
            <flux:table.row wire:key="ingredient-{{ $loop->index }}">
              <flux:table.cell class="truncate max-w-48">
                <x-flag :code="$ingredient->country_code" :title="$ingredient->country_code" />
                {{ $ingredientName }}
              </flux:table.cell>
              <flux:table.cell align="end" class="tabular-nums">{{ Number::format($ingredient->recipes_count) }}</flux:table.cell>
            </flux:table.row>
          @endforeach
        </flux:table.rows>
      </flux:table>
    </flux:card>

    <flux:card class="space-y-section">
      <flux:heading size="lg">Top 10 Tags</flux:heading>
      <flux:table>
        <flux:table.columns>
          <flux:table.column class="ui-text-subtle">Tag</flux:table.column>
          <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
          @foreach($this->topTags as $tag)
            @php
              $locales = json_decode($tag->country_locales ?? '["en"]', true);
              $names = is_string($tag->name) ? json_decode($tag->name, true) : $tag->name;
              $tagName = $names[$locales[0] ?? 'en'] ?? (is_array($names) ? array_values($names)[0] ?? '' : '');
            @endphp
            <flux:table.row wire:key="tag-{{ $loop->index }}">
              <flux:table.cell class="truncate max-w-48">
                <x-flag :code="$tag->country_code" :title="$tag->country_code" />
                {{ $tagName }}
              </flux:table.cell>
              <flux:table.cell align="end" class="tabular-nums">{{ Number::format($tag->recipes_count) }}</flux:table.cell>
            </flux:table.row>
          @endforeach
        </flux:table.rows>
      </flux:table>
    </flux:card>

    <flux:card class="space-y-section">
      <flux:heading size="lg">Top 10 Cuisines</flux:heading>
      <flux:table>
        <flux:table.columns>
          <flux:table.column class="ui-text-subtle">Cuisine</flux:table.column>
          <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
          @foreach($this->topCuisines as $cuisine)
            @php
              $locales = json_decode($cuisine->country_locales ?? '["en"]', true);
              $names = is_string($cuisine->name) ? json_decode($cuisine->name, true) : $cuisine->name;
              $cuisineName = $names[$locales[0] ?? 'en'] ?? (is_array($names) ? array_values($names)[0] ?? '' : '');
            @endphp
            <flux:table.row wire:key="cuisine-{{ $loop->index }}">
              <flux:table.cell class="truncate max-w-48">
                <x-flag :code="$cuisine->country_code" :title="$cuisine->country_code" />
                {{ $cuisineName }}
              </flux:table.cell>
              <flux:table.cell align="end" class="tabular-nums">{{ Number::format($cuisine->recipes_count) }}</flux:table.cell>
            </flux:table.row>
          @endforeach
        </flux:table.rows>
      </flux:table>
    </flux:card>
  </div>

  {{-- Recipes per Month --}}
  <flux:card>
    <flux:heading size="lg">Recipes Added (Last 12 Months)</flux:heading>
    @php
      $maxCount = $this->recipesPerMonth->max('count') ?: 1;
      $totalCount = $this->recipesPerMonth->sum('count') ?: 1;
    @endphp
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Month</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Share</flux:table.column>
        <flux:table.column class="ui-text-subtle w-1/2">Distribution</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->recipesPerMonth as $month)
          @php
            $barWidth = ($month->count / $maxCount) * 100;
            $sharePercent = ($month->count / $totalCount) * 100;
          @endphp
          <flux:table.row wire:key="month-{{ $month->month }}">
            <flux:table.cell class="font-medium">{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ Number::format($month->count) }}</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums text-zinc-500">{{ Number::format($sharePercent, 1) }}%</flux:table.cell>
            <flux:table.cell>
              <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div class="bg-lime-500 dark:bg-lime-600 h-2 rounded-full" style="width: {{ $barWidth }}%"></div>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>

  {{-- Average Prep Times by Country --}}
  <flux:card>
    <flux:heading size="lg">Average Prep Times by Country</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Country</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Avg. Prep Time</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Avg. Total Time</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->avgPrepTimesByCountry as $country)
          <flux:table.row wire:key="prep-{{ $country->code }}">
            <flux:table.cell>
              <span class="font-medium"><x-flag :code="$country->code" /> {{ $country->code }}</span>
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ $country->avg_prep }} min</flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ $country->avg_total }} min</flux:table.cell>
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
              {{ Number::format($item['count']) }} ({{ Number::format($percentage, 1) }}%)
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

  <flux:text class="text-sm text-zinc-500 text-center">
    Statistics are cached and refresh every hour.
  </flux:text>
</flux:main>
