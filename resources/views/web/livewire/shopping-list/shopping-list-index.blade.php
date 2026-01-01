<flux:main
  container
  class="space-y-section"
  x-data="{
        initialized: false,
        init() {
            this.$nextTick(() => {
                this.syncFromLocalStorage();
                this.initialized = true;
            });

            window.addEventListener('storage', () => this.syncFromLocalStorage());
            window.addEventListener('shopping-list-updated', () => this.syncFromLocalStorage());
        },
        syncFromLocalStorage() {
            const items = $store.shoppingList.items;
            const servings = $store.shoppingList.servings;
            $wire.loadRecipes(items, servings);
        },
        updateServings(recipeId, servings) {
            $store.shoppingList.setServings(recipeId, servings);
            this.syncFromLocalStorage();
        },
        confirmRemoveRecipe(recipeId, recipeName) {
            window.dispatchEvent(new CustomEvent('confirm-action', {
                detail: {
                    title: '{{ __('Remove Recipe') }}',
                    message: '{{ __('Remove :name from shopping list?', ['name' => '']) }}' + recipeName,
                    confirmText: '{{ __('Remove') }}',
                    onConfirm: () => $store.shoppingList.remove(recipeId)
                }
            }));
        },
        confirmClearAll() {
            window.dispatchEvent(new CustomEvent('confirm-action', {
                detail: {
                    title: '{{ __('Clear Shopping List') }}',
                    message: '{{ __('Remove all recipes from your shopping list?') }}',
                    confirmText: '{{ __('Clear All') }}',
                    onConfirm: () => $store.shoppingList.clear()
                }
            }));
        },
        printStyle: 'combined',
        printList(style) {
            this.printStyle = style;
            this.$nextTick(() => window.print());
        },
        exportToBring() {
            const items = $store.shoppingList.items;
            const servings = $store.shoppingList.servings;

            if (items.length === 0) return;

            const recipesParam = items.join(',');
            const servingsParam = items.map(id => id + ':' + (servings[id] || 2)).join(',');

            const bringUrl = '{{ localized_route('localized.shopping-list.bring') }}'
                + '?recipes=' + encodeURIComponent(recipesParam)
                + '&servings=' + encodeURIComponent(servingsParam);

            const url = 'https://api.getbring.com/rest/bringrecipes/deeplink'
                + '?url=' + encodeURIComponent(bringUrl)
                + '&source=web';

            window.open(url, '_blank');
        }
    }"
>
  @if ($this->isPrintMode())
    {{-- Print View --}}
    <div class="print:block">
      <div class="flex items-center justify-between mb-section print:hidden">
        <flux:heading size="xl">{{ __('Shopping List') }}</flux:heading>
        <div class="flex items-center gap-ui">
          <flux:button icon="printer" variant="primary" x-on:click="window.print()">
            {{ __('Print') }}
          </flux:button>
          <flux:button icon="x" :href="localized_route('localized.shopping-list.index')">
            {{ __('Close') }}
          </flux:button>
        </div>
      </div>

      <div class="hidden print:block mb-4">
        <h1 class="text-2xl font-bold">{{ __('Shopping List') }}</h1>
        <p class="text-sm text-zinc-500">{{ now()->format('d.m.Y H:i') }}</p>
      </div>

      @if ($printStyle === 'combined')
        {{-- Combined ingredients list --}}
        <div class="space-y-1">
          @foreach ($this->aggregatedIngredients as $data)
            <div class="flex items-center gap-3 py-1 border-b border-zinc-200">
              <input type="checkbox" class="size-4 rounded border-zinc-300">
              <span class="flex-1">{{ $data['ingredient']->name }}</span>
              <span class="font-medium tabular-nums">
                @if ($data['total'] > 0)
                  {{ Number::format($data['total']) }} {{ $data['unit'] }}
                @else
                  {{ $data['unit'] }}
                @endif
              </span>
            </div>
          @endforeach
        </div>
      @elseif ($printStyle === 'by-recipe')
        {{-- Ingredients grouped by recipe --}}
        @foreach ($this->recipes as $recipe)
          <div class="mb-section">
            <h2 class="text-lg font-semibold mb-2">
              {{ $recipe->name }}
              <span class="text-sm font-normal text-zinc-500">({{ $this->getServingsForRecipe($recipe) }}p)</span>
            </h2>
            <div class="space-y-1 pl-4">
              @php
                $servings = $this->getServingsForRecipe($recipe);
                $yieldsData = $this->getYieldsDataForServings($recipe, $servings);
              @endphp
              @foreach ($recipe->ingredients as $ingredient)
                @php
                  $ingredientData = $this->findIngredientInYieldsForIngredient($yieldsData, $ingredient);
                @endphp
                <div class="flex items-center gap-3 py-1 border-b border-zinc-200">
                  <input type="checkbox" class="size-4 rounded border-zinc-300">
                  <span class="flex-1">{{ $ingredient->name }}</span>
                  <span class="font-medium tabular-nums">
                    @if ($ingredientData['amount'] !== null)
                      {{ Number::format($ingredientData['amount']) }} {{ $ingredientData['unit'] }}
                    @else
                      {{ $ingredientData['unit'] }}
                    @endif
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      @elseif ($printStyle === 'combined-no-checkbox')
        {{-- Combined without checkboxes --}}
        <div class="space-y-1">
          @foreach ($this->aggregatedIngredients as $data)
            <div class="flex items-center gap-3 py-1 border-b border-zinc-200">
              <span class="flex-1">{{ $data['ingredient']->name }}</span>
              <span class="font-medium tabular-nums">
                @if ($data['total'] > 0)
                  {{ Number::format($data['total']) }} {{ $data['unit'] }}
                @else
                  {{ $data['unit'] }}
                @endif
              </span>
            </div>
          @endforeach
        </div>
      @elseif ($printStyle === 'by-recipe-no-checkbox')
        {{-- By recipe without checkboxes --}}
        @foreach ($this->recipes as $recipe)
          <div class="mb-section">
            <h2 class="text-lg font-semibold mb-2">
              {{ $recipe->name }}
              <span class="text-sm font-normal text-zinc-500">({{ $this->getServingsForRecipe($recipe) }}p)</span>
            </h2>
            <div class="space-y-1 pl-4">
              @php
                $servings = $this->getServingsForRecipe($recipe);
                $yieldsData = $this->getYieldsDataForServings($recipe, $servings);
              @endphp
              @foreach ($recipe->ingredients as $ingredient)
                @php
                  $ingredientData = $this->findIngredientInYieldsForIngredient($yieldsData, $ingredient);
                @endphp
                <div class="flex items-center gap-3 py-1 border-b border-zinc-200">
                  <span class="flex-1">{{ $ingredient->name }}</span>
                  <span class="font-medium tabular-nums">
                    @if ($ingredientData['amount'] !== null)
                      {{ Number::format($ingredientData['amount']) }} {{ $ingredientData['unit'] }}
                    @else
                      {{ $ingredientData['unit'] }}
                    @endif
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      @endif
    </div>
  @else
    {{-- Regular View --}}
    <div class="flex items-center justify-between print:hidden">
      {{-- Desktop: Heading --}}
      <flux:heading size="xl" class="hidden sm:block">{{ __('Shopping List') }}</flux:heading>

      {{-- Mobile: Badge left, Buttons right --}}
      <div x-show="$store.shoppingList.count > 0" class="flex items-center gap-4 sm:hidden w-full">
        <flux:badge color="green" x-text="$store.shoppingList.count + ' ' + ($store.shoppingList.count === 1 ? '{{ __('Recipe') }}' : '{{ __('Recipes') }}')"></flux:badge>

        <div class="flex items-center gap-ui ml-auto">
          <flux:dropdown>
            <flux:button size="sm" icon="printer" square />
            <flux:menu>
              <flux:menu.heading>{{ __('With Checkboxes') }}</flux:menu.heading>
              <flux:menu.item icon="list" x-on:click="printList('combined')">
                {{ __('All ingredients combined') }}
              </flux:menu.item>
              <flux:menu.item icon="list" x-on:click="printList('by-recipe')">
                {{ __('Grouped by recipe') }}
              </flux:menu.item>
              <flux:menu.separator />
              <flux:menu.heading>{{ __('Without Checkboxes') }}</flux:menu.heading>
              <flux:menu.item icon="list" x-on:click="printList('combined-no-checkbox')">
                {{ __('All ingredients combined') }}
              </flux:menu.item>
              <flux:menu.item icon="list" x-on:click="printList('by-recipe-no-checkbox')">
                {{ __('Grouped by recipe') }}
              </flux:menu.item>
            </flux:menu>
          </flux:dropdown>

          <flux:button size="sm" icon="bring" square x-on:click="exportToBring()" title="Bring!" />

          <flux:button size="sm" icon="bookmark" square wire:click="openSaveModal" />

          <flux:button variant="danger" size="sm" icon="trash" square x-on:click="confirmClearAll()" />
        </div>
      </div>

      {{-- Desktop: All buttons with text --}}
      <div x-show="$store.shoppingList.count > 0" class="hidden sm:flex items-center gap-ui">
        <flux:badge color="green" x-text="$store.shoppingList.count + ' ' + ($store.shoppingList.count === 1 ? '{{ __('Recipe') }}' : '{{ __('Recipes') }}')"></flux:badge>

        <flux:dropdown>
          <flux:button variant="subtle" size="sm" icon="printer">
            {{ __('Print') }}
          </flux:button>

          <flux:menu>
            <flux:menu.heading>{{ __('With Checkboxes') }}</flux:menu.heading>
            <flux:menu.item icon="list" x-on:click="printList('combined')">
              {{ __('All ingredients combined') }}
            </flux:menu.item>
            <flux:menu.item icon="list" x-on:click="printList('by-recipe')">
              {{ __('Grouped by recipe') }}
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.heading>{{ __('Without Checkboxes') }}</flux:menu.heading>
            <flux:menu.item icon="list" x-on:click="printList('combined-no-checkbox')">
              {{ __('All ingredients combined') }}
            </flux:menu.item>
            <flux:menu.item icon="list" x-on:click="printList('by-recipe-no-checkbox')">
              {{ __('Grouped by recipe') }}
            </flux:menu.item>
          </flux:menu>
        </flux:dropdown>

        <flux:button variant="subtle" size="sm" icon="bring" x-on:click="exportToBring()">
          Bring!
        </flux:button>

        <flux:button variant="subtle" size="sm" icon="bookmark" wire:click="openSaveModal">
          {{ __('Save') }}
        </flux:button>

        <flux:button variant="danger" size="sm" icon="trash" x-on:click="confirmClearAll()">
          {{ __('Clear All') }}
        </flux:button>
      </div>
    </div>

    <template x-if="$store.shoppingList.isEmpty">
      <flux:card class="text-center py-12">
        <flux:icon.shopping-basket class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
        <flux:heading size="lg" class="mt-4">{{ __('Your shopping list is empty') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Add recipes from the recipe overview to get started.') }}</flux:text>
        <flux:button :href="localized_route('localized.recipes.index')" variant="primary" class="mt-4">
          {{ __('Browse Recipes') }}
        </flux:button>
      </flux:card>
    </template>

    <template x-if="!$store.shoppingList.isEmpty">
      <div class="space-y-section">
        {{-- Recipes Section --}}
        <flux:card>
          <flux:heading size="lg">{{ __('Recipes') }}</flux:heading>

          <div class="-mx-6 mt-4">
            @foreach ($this->recipes as $recipe)
              <div wire:key="recipe-{{ $recipe->id }}" class="flex gap-4 px-6 py-4 border-t border-zinc-300 dark:border-zinc-600">
                @if ($recipe->card_image_url)
                  <img
                    src="{{ $recipe->card_image_url }}"
                    alt="{{ $recipe->name }}"
                    class="size-16 shrink-0 rounded object-cover"
                  >
                @endif

                <div class="flex-1">
                  <flux:link :href="localized_route('localized.recipes.show', ['slug' => slugify($recipe->name), 'recipe' => $recipe->id])" wire:navigate class="print:hidden">
                    {{ $recipe->name }}
                  </flux:link>
                  <flux:heading size="sm" class="not-print:hidden">{{ $recipe->name }}</flux:heading>

                  <div class="flex items-center gap-ui mt-2 justify-end">
                    <flux:button.group>
                      @foreach ($this->getYieldsForRecipe($recipe) as $yield)
                        <flux:button
                          size="sm"
                          :variant="$this->getServingsForRecipe($recipe) === $yield ? 'primary' : null"
                          :class="$this->getServingsForRecipe($recipe) === $yield ? '' : 'print:hidden'"
                          x-on:click="updateServings({{ $recipe->id }}, {{ $yield }})"
                        >
                          {{ $yield }}p
                        </flux:button>
                      @endforeach
                    </flux:button.group>

                    <flux:button
                      variant="danger"
                      size="sm"
                      icon="x"
                      class="print:hidden"
                      x-on:click="confirmRemoveRecipe({{ $recipe->id }}, '{{ addslashes($recipe->name) }}')"
                    />
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </flux:card>

        {{-- Ingredients Section --}}
        @if (count($this->aggregatedIngredients) > 0)
          <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Ingredients') }}</flux:heading>

            <div class="-mx-4 sm:-mx-6">
              @foreach ($this->aggregatedIngredients as $data)
                <div
                  wire:key="ingredient-{{ $data['ingredient']->id }}"
                  class="flex items-start gap-3 px-4 sm:px-6 py-3 border-t border-zinc-300 dark:border-zinc-600"
                >
                  @if ($data['ingredient']->image_path)
                    <img
                      src="{{ \App\Support\HelloFresh\HelloFreshAsset::ingredientThumbnail($data['ingredient']->image_path) }}"
                      alt="{{ $data['ingredient']->name }}"
                      class="size-10 shrink-0 rounded object-cover"
                    >
                  @else
                    <div class="size-10 shrink-0 rounded bg-zinc-100 dark:bg-zinc-700"></div>
                  @endif

                  <div class="flex-1 min-w-0">
                    <flux:heading size="sm">{{ $data['ingredient']->name }}</flux:heading>

                    <div class="mt-1 space-y-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                      @foreach ($data['items'] as $item)
                        <div class="flex justify-between gap-4">
                          <span class="truncate">{{ $item['recipe']->name }} ({{ $item['servings'] }}p)</span>
                          <span class="shrink-0 tabular-nums">
                            @if ($item['amount'] !== null)
                              {{ Number::format($item['amount']) }} {{ $item['unit'] }}
                            @else
                              {{ $item['unit'] }}
                            @endif
                          </span>
                        </div>
                      @endforeach
                    </div>
                  </div>

                  <div class="shrink-0 w-24 text-right tabular-nums">
                    <span class="font-semibold text-lg">
                      @if ($data['total'] > 0)
                        {{ Number::format($data['total']) }} {{ $data['unit'] }}
                      @else
                        {{ $data['unit'] }}
                      @endif
                    </span>
                  </div>
                </div>
              @endforeach
            </div>
          </flux:card>
        @endif
      </div>
    </template>

    {{-- Save Shopping List Modal --}}
    <flux:modal name="save-shopping-list" class="max-w-md space-y-section">
      <flux:heading size="lg">{{ __('Save Shopping List') }}</flux:heading>

      <form wire:submit="saveList" class="space-y-section">
        <flux:input
          wire:model="saveListName"
          :label="__('List Name')"
          :placeholder="__('Week :week', ['week' => now()->weekOfYear])"
          required
        />

        <div class="flex justify-end gap-ui">
          <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
      </form>
    </flux:modal>

    {{-- Print-only content (hidden on screen, visible when printing) --}}
    <div class="hidden print:block">
      <div class="mb-4">
        <h1 class="text-2xl font-bold">{{ __('Shopping List') }}</h1>
        <p class="text-sm text-zinc-500">{{ now()->format('d.m.Y H:i') }}</p>
      </div>

      {{-- Combined with checkboxes --}}
      <div x-show="printStyle === 'combined'" class="space-y-1">
        @foreach ($this->aggregatedIngredients as $data)
          <div class="flex items-center gap-3 py-1 border-b border-zinc-300">
            <input type="checkbox" class="size-4 rounded border-zinc-400">
            <span class="flex-1">{{ $data['ingredient']->name }}</span>
            <span class="font-medium tabular-nums">
              @if ($data['total'] > 0)
                {{ Number::format($data['total']) }} {{ $data['unit'] }}
              @else
                {{ $data['unit'] }}
              @endif
            </span>
          </div>
        @endforeach
      </div>

      {{-- By recipe with checkboxes --}}
      <div x-show="printStyle === 'by-recipe'">
        @foreach ($this->recipes as $recipe)
          <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">
              {{ $recipe->name }}
              <span class="text-sm font-normal text-zinc-500">({{ $this->getServingsForRecipe($recipe) }}p)</span>
            </h2>
            <div class="space-y-1 pl-4">
              @php
                $servings = $this->getServingsForRecipe($recipe);
                $yieldsData = $this->getYieldsDataForServings($recipe, $servings);
              @endphp
              @foreach ($recipe->ingredients as $ingredient)
                @php
                  $ingredientData = $this->findIngredientInYieldsForIngredient($yieldsData, $ingredient);
                @endphp
                <div class="flex items-center gap-3 py-1 border-b border-zinc-300">
                  <input type="checkbox" class="size-4 rounded border-zinc-400">
                  <span class="flex-1">{{ $ingredient->name }}</span>
                  <span class="font-medium tabular-nums">
                    @if ($ingredientData['amount'] !== null)
                      {{ Number::format($ingredientData['amount']) }} {{ $ingredientData['unit'] }}
                    @else
                      {{ $ingredientData['unit'] }}
                    @endif
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      {{-- Combined without checkboxes --}}
      <div x-show="printStyle === 'combined-no-checkbox'" class="space-y-1">
        @foreach ($this->aggregatedIngredients as $data)
          <div class="flex items-center gap-3 py-1 border-b border-zinc-300">
            <span class="flex-1">{{ $data['ingredient']->name }}</span>
            <span class="font-medium tabular-nums">
              @if ($data['total'] > 0)
                {{ Number::format($data['total']) }} {{ $data['unit'] }}
              @else
                {{ $data['unit'] }}
              @endif
            </span>
          </div>
        @endforeach
      </div>

      {{-- By recipe without checkboxes --}}
      <div x-show="printStyle === 'by-recipe-no-checkbox'">
        @foreach ($this->recipes as $recipe)
          <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">
              {{ $recipe->name }}
              <span class="text-sm font-normal text-zinc-500">({{ $this->getServingsForRecipe($recipe) }}p)</span>
            </h2>
            <div class="space-y-1 pl-4">
              @php
                $servings = $this->getServingsForRecipe($recipe);
                $yieldsData = $this->getYieldsDataForServings($recipe, $servings);
              @endphp
              @foreach ($recipe->ingredients as $ingredient)
                @php
                  $ingredientData = $this->findIngredientInYieldsForIngredient($yieldsData, $ingredient);
                @endphp
                <div class="flex items-center gap-3 py-1 border-b border-zinc-300">
                  <span class="flex-1">{{ $ingredient->name }}</span>
                  <span class="font-medium tabular-nums">
                    @if ($ingredientData['amount'] !== null)
                      {{ Number::format($ingredientData['amount']) }} {{ $ingredientData['unit'] }}
                    @else
                      {{ $ingredientData['unit'] }}
                    @endif
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</flux:main>
