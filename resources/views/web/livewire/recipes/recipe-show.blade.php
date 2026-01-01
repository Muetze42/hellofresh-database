<flux:main container>
  {{-- Header with image --}}
  <div class="relative -mx-4 -mt-4 sm:-mx-6 sm:-mt-6 lg:-mx-8 lg:-mt-8">
    @if ($recipe->header_image_url)
      <img
        src="{{ $recipe->header_image_url }}"
        alt="{{ $recipe->name }}"
        class="w-full h-64 sm:h-80 lg:h-96 object-cover"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
    @endif

    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8 text-white">
      <div class="flex items-center gap-2 mb-2">
        @if ($recipe->label && $recipe->label->display_label)
          <span
            class="rounded px-2 py-1 text-xs font-semibold"
            style="background-color: {{ $recipe->label->background_color }}; color: {{ $recipe->label->foreground_color }}"
          >
            {{ $recipe->label->name }}
          </span>
        @endif
        @foreach ($recipe->tags->where('display_label', true)->take(3) as $tag)
          <flux:badge size="sm">{{ $tag->name }}</flux:badge>
        @endforeach
      </div>
      <flux:heading size="2xl" class="text-white!">{{ $recipe->name }}</flux:heading>
      @if ($recipe->headline)
        <flux:text class="mt-1 text-white/80">{{ $recipe->headline }}</flux:text>
      @endif
    </div>
  </div>

  {{-- Quick info bar --}}
  <div class="flex flex-wrap items-center gap-4 py-4 border-b border-zinc-200 dark:border-zinc-700">
    @if ($recipe->total_time)
      <div class="flex items-center gap-2">
        <flux:icon.clock variant="mini" class="text-zinc-500" />
        <span>{{ $recipe->total_time }} {{ __('min') }}</span>
      </div>
    @endif
    @if ($recipe->difficulty)
      <div class="flex items-center gap-2">
        <flux:icon.chart-bar-big variant="mini" class="text-zinc-500" />
        <span>{{ __('Difficulty') }}: {{ $recipe->difficulty }}/3</span>
      </div>
    @endif
    @if ($recipe->cuisines->isNotEmpty())
      <div class="flex items-center gap-2">
        <flux:icon.globe variant="mini" class="text-zinc-500" />
        <span>{{ $recipe->cuisines->pluck('name')->join(', ') }}</span>
      </div>
    @endif

    <div class="flex items-center gap-2 ml-auto">
      @if ($recipe->hellofresh_url)
        <a
          href="{{ $recipe->hellofresh_url }}"
          target="_blank"
          rel="noopener noreferrer"
          class="rounded-full p-2 transition-colors bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
          title="{{ __('View on HelloFresh') }}"
        >
          <flux:icon.external-link variant="mini" />
        </a>
      @endif
      @if ($recipe->pdf_url)
        <a
          href="{{ $recipe->pdf_url }}"
          target="_blank"
          rel="noopener noreferrer"
          class="rounded-full p-2 transition-colors bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
          title="{{ __('View PDF') }}"
        >
          <flux:icon.file-text variant="mini" />
        </a>
      @endif
      <livewire:web.recipes.add-to-list-button :recipe-id="$recipe->id" />
      <button
        type="button"
        x-data
        x-on:click.prevent.stop="$store.shoppingList?.toggle({{ $recipe->id }})"
        class="rounded-full p-2 transition-colors bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
        x-bind:class="$store.shoppingList?.has({{ $recipe->id }}) && 'bg-green-500! text-white! hover:bg-green-600!'"
        x-bind:title="$store.shoppingList?.has({{ $recipe->id }}) ? '{{ __('Remove from shopping list') }}' : '{{ __('Add to shopping list') }}'"
      >
        <flux:icon.shopping-basket variant="mini" />
      </button>
    </div>
  </div>

  {{-- Description --}}
  @if ($recipe->description)
    <div class="pt-section">
      <flux:text>{{ $recipe->description }}</flux:text>
    </div>
  @endif

  {{-- Info blocks: Allergens, Utensils, Tags --}}
  @if ($recipe->allergens->isNotEmpty() || $recipe->utensils->isNotEmpty() || $recipe->tags->isNotEmpty())
    <div class="space-y-section py-section border-b border-zinc-200 dark:border-zinc-700">
      @if ($recipe->allergens->isNotEmpty())
        <div>
          <flux:text class="text-sm text-zinc-500 mb-ui">{{ __('Allergens') }}</flux:text>
          <div class="flex flex-wrap gap-1">
            @foreach ($recipe->allergens as $allergen)
              <flux:badge size="sm" color="red">{{ $allergen->name }}</flux:badge>
            @endforeach
          </div>
        </div>
      @endif

      @if ($recipe->utensils->isNotEmpty())
        <div>
          <flux:text class="text-sm text-zinc-500 mb-ui">{{ __('Utensils') }}</flux:text>
          <div class="flex flex-wrap gap-1">
            @foreach ($recipe->utensils as $utensil)
              <flux:badge size="sm" color="zinc">{{ $utensil->name }}</flux:badge>
            @endforeach
          </div>
        </div>
      @endif

      @if ($recipe->tags->isNotEmpty())
        <div>
          <flux:text class="text-sm text-zinc-500 mb-ui">{{ __('Tags') }}</flux:text>
          <div class="flex flex-wrap gap-1">
            @foreach ($recipe->tags as $tag)
              <flux:badge size="sm" color="lime">{{ $tag->name }}</flux:badge>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-section py-section">
    {{-- Left column: Ingredients --}}
    <div class="lg:col-span-1">
      <flux:card class="lg:sticky lg:top-20 lg:max-h-[calc(100vh-6rem)] lg:overflow-auto">
        <div class="flex items-center justify-between mb-4">
          <flux:heading size="lg">{{ __('Ingredients') }}</flux:heading>
          @if (count($this->availableYields) > 1)
            <flux:button.group>
              @foreach ($this->availableYields as $yield)
                <flux:button
                  size="sm"
                  :variant="$selectedYield === $yield ? 'primary' : 'outline'"
                  wire:click="$set('selectedYield', {{ $yield }})"
                >
                  {{ $yield }}
                </flux:button>
              @endforeach
            </flux:button.group>
          @endif
        </div>

        <div class="space-y-3">
          @foreach ($this->ingredientsForYield as $item)
            @if ($item['ingredient'])
              <div class="flex items-center gap-3">
                @if ($item['ingredient']->image_path)
                  <img
                    src="{{ \App\Support\HelloFresh\HelloFreshAsset::ingredientThumbnail($item['ingredient']->image_path) }}"
                    alt="{{ $item['ingredient']->name }}"
                    class="size-10 rounded object-cover"
                  >
                @else
                  <div class="size-10 rounded bg-zinc-100 dark:bg-zinc-800"></div>
                @endif
                <div class="flex-1">
                  <flux:text class="font-medium">{{ $item['ingredient']->name }}</flux:text>
                </div>
                <flux:text class="text-zinc-500 shrink-0">
                  @if ($item['amount'])
                    {{ $item['amount'] }} {{ $item['unit'] }}
                  @else
                    {{ $item['unit'] }}
                  @endif
                </flux:text>
              </div>
            @endif
          @endforeach
        </div>
      </flux:card>
    </div>

    {{-- Right column: Steps --}}
    <div class="lg:col-span-2 space-y-section">
      <flux:heading size="lg">{{ __('Preparation') }}</flux:heading>

      @foreach ($this->steps as $step)
        <div class="flex gap-4">
          <div class="shrink-0 size-8 rounded-full bg-lime-500 text-white flex items-center justify-center font-semibold">
            {{ $step['index'] }}
          </div>
          <div class="flex-1 space-y-3">
            @if (!empty($step['images']))
              <div class="flex gap-2 overflow-x-auto">
                @foreach ($step['images'] as $image)
                  <img
                    src="{{ \App\Support\HelloFresh\HelloFreshAsset::stepImage($image['path']) }}"
                    alt="{{ $image['caption'] ?? '' }}"
                    class="h-32 rounded object-cover shrink-0"
                  >
                @endforeach
              </div>
            @endif
            <flux:text>
              {!! $step['instructions'] !!}
            </flux:text>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Nutrition --}}
  @if (!empty($this->nutrition))
    <div class="py-section border-t border-zinc-200 dark:border-zinc-700">
      <flux:heading size="lg" class="mb-4">{{ __('Nutrition') }} <span class="text-sm font-normal text-zinc-500">{{ __('per serving') }}</span></flux:heading>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach ($this->nutrition as $nutrient)
          <flux:card class="text-center">
            <flux:text class="text-2xl font-bold">{{ $nutrient['amount'] }}</flux:text>
            <flux:text class="text-sm text-zinc-500">{{ $nutrient['unit'] }}</flux:text>
            <flux:text class="text-sm font-medium mt-1">{{ $nutrient['name'] }}</flux:text>
          </flux:card>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Similar Recipes --}}
  @if ($this->similarRecipes->isNotEmpty())
    <div class="py-section border-t border-zinc-200 dark:border-zinc-700">
      <flux:heading size="lg" class="mb-4">{{ __('Similar Recipes') }}</flux:heading>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-section">
        @foreach ($this->similarRecipes as $similarRecipe)
          <flux:card class="overflow-hidden">
            @if ($similarRecipe->card_image_url)
              <img
                src="{{ $similarRecipe->card_image_url }}"
                alt="{{ $similarRecipe->name }}"
                class="aspect-video w-full object-cover"
              >
            @endif
            <div class="p-4">
              <flux:heading size="sm" class="line-clamp-2">
                <flux:link :href="localized_route('localized.recipes.show', ['slug' => slugify($similarRecipe->name), 'recipe' => $similarRecipe->id])">
                  {{ $similarRecipe->name }}
                </flux:link>
              </flux:heading>
            </div>
          </flux:card>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Back link --}}
  <div class="py-section">
    <flux:button :href="localized_route('localized.recipes.index')" variant="ghost" icon="arrow-left">
      {{ __('Back to recipes') }}
    </flux:button>
  </div>
</flux:main>
