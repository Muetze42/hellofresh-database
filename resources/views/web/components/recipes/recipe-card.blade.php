@if ($viewMode === \App\Enums\ViewModeEnum::Grid)
  <flux:card class="flex flex-col gap-ui">
    <div class="block relative">
      @if ($recipe->card_image_url)
        <img
          src="{{ $recipe->card_image_url }}"
          alt="{{ $recipe->name }}"
          class="aspect-video w-full object-cover"
        >
      @endif

      <div class="absolute top-2 right-2 flex items-center gap-1">
        @if ($recipe->hellofresh_url)
          <a
            href="{{ $recipe->hellofresh_url }}"
            target="_blank"
            rel="noopener noreferrer"
            class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
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
            class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
            title="{{ __('View PDF') }}"
          >
            <flux:icon.file-text variant="mini" />
          </a>
        @endif
        <livewire:web.recipes.add-to-list-button :recipe-id="$recipe->id" :key="'list-grid-'.$recipe->id" />
        <button
          type="button"
          x-data
          x-on:click.prevent.stop="$store.shoppingList?.toggle({{ $recipe->id }})"
          class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
          x-bind:class="$store.shoppingList?.has({{ $recipe->id }}) && 'bg-green-500! text-white! hover:bg-green-600!'"
          x-bind:title="$store.shoppingList?.has({{ $recipe->id }}) ? '{{ __('Remove from shopping list') }}' : '{{ __('Add to shopping list') }}'"
        >
          <flux:icon.shopping-basket variant="mini" />
        </button>
      </div>

      @if ($recipe->label && $recipe->label->display_label)
        <div
          class="absolute bottom-2 left-2 rounded px-2 py-1 text-xs font-semibold"
          style="background-color: {{ $recipe->label->background_color }}; color: {{ $recipe->label->foreground_color }}"
        >
          {{ $recipe->label->name }}
        </div>
      @endif
    </div>

    <div class="grow">
      <flux:heading size="">
        <flux:link :href="localized_route('localized.recipes.show', ['slug' => slugify($recipe->name), 'recipe' => $recipe->id])" wire:navigate>
          {{ $recipe->name }}
        </flux:link>
      </flux:heading>

      @if ($recipe->headline)
        <flux:text class="mt-1 line-clamp-2">{{ $recipe->headline }}</flux:text>
      @endif

    </div>
    <div>

      <div class="mt-3 flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
        @if ($recipe->total_time)
          <span class="flex items-center gap-1">
            <flux:icon.clock variant="micro" />
            {{ $recipe->total_time }} {{ __('min') }}
          </span>
        @endif

        @if ($recipe->difficulty)
          <span class="flex items-center gap-1">
            <flux:icon.chart-bar-big variant="micro" />
            {{ $recipe->difficulty }}/3
          </span>
        @endif
      </div>

      @if ($recipe->tags->where('display_label', true)->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1">
          @foreach ($recipe->tags->where('display_label', true) as $tag)
            @php $isActive = in_array($tag->id, $tagIds ?? []); @endphp
            {{-- Clickable disabled: zinc --}}
            <flux:badge
              x-data
              x-show="!$store.settings?.clickableTags"
              x-cloak
              color="zinc"
              size="sm"
            >{{ $tag->name }}</flux:badge>
            {{-- Clickable enabled: lime if active, zinc with plus icon if not --}}
            <flux:badge
              x-data
              x-show="$store.settings?.clickableTags"
              x-cloak
              x-on:click.stop="$wire.toggleTag({{ $tag->id }})"
              as="button"
              :color="$isActive ? 'lime' : 'zinc'"
              icon:variant="micro"
              size="sm"
              wire:loading.attr="disabled"
              wire:target="toggleTag({{ $tag->id }})"
            >
              <flux:icon.loader-circle variant="micro" class="animate-spin" wire:loading wire:target="toggleTag({{ $tag->id }})" />
              <flux:icon.check variant="micro" wire:loading.remove wire:target="toggleTag({{ $tag->id }})" @class(['hidden' => !$isActive]) />
              <flux:icon.plus variant="micro" wire:loading.remove wire:target="toggleTag({{ $tag->id }})" @class(['hidden' => $isActive]) />
              {{ $tag->name }}
            </flux:badge>
          @endforeach
        </div>
      @endif
    </div>
  </flux:card>
@else
  <flux:card class="flex gap-4 overflow-hidden">
    <div class="relative shrink-0">
      @if ($recipe->card_image_url)
        <img
          src="{{ $recipe->card_image_url }}"
          alt="{{ $recipe->name }}"
          class="size-24 rounded object-cover sm:size-32"
        >
      @endif

    </div>

    <div class="flex flex-1 flex-col justify-center py-2">
      <div class="flex items-center gap-2">
        <flux:heading size="lg" class="line-clamp-1">
          <flux:link :href="localized_route('localized.recipes.show', ['slug' => slugify($recipe->name), 'recipe' => $recipe->id])" wire:navigate>
            {{ $recipe->name }}
          </flux:link>
        </flux:heading>
        @if ($recipe->label && $recipe->label->display_label)
          <span
            class="shrink-0 rounded px-1.5 py-0.5 text-xs font-semibold"
            style="background-color: {{ $recipe->label->background_color }}; color: {{ $recipe->label->foreground_color }}"
          >
            {{ $recipe->label->name }}
          </span>
        @endif
      </div>

      @if ($recipe->headline)
        <flux:text class="mt-1 line-clamp-2">{{ $recipe->headline }}</flux:text>
      @endif

      <div class="mt-2 flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
        @if ($recipe->total_time)
          <span class="flex items-center gap-1">
            <flux:icon.clock variant="micro" />
            {{ $recipe->total_time }} {{ __('min') }}
          </span>
        @endif

        @if ($recipe->difficulty)
          <span class="flex items-center gap-1">
            <flux:icon.chart-bar-big variant="micro" />
            {{ $recipe->difficulty }}/3
          </span>
        @endif
      </div>

      @if ($recipe->tags->where('display_label', true)->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1">
          @foreach ($recipe->tags->where('display_label', true) as $tag)
            @php $isActive = in_array($tag->id, $tagIds ?? []); @endphp
            {{-- Clickable disabled: zinc --}}
            <flux:badge
              x-data
              x-show="!$store.settings?.clickableTags"
              x-cloak
              color="zinc"
              size="sm"
            >{{ $tag->name }}</flux:badge>
            {{-- Clickable enabled: lime if active, zinc with plus icon if not --}}
            <flux:badge
              x-data
              x-show="$store.settings?.clickableTags"
              x-cloak
              x-on:click.stop="$wire.toggleTag({{ $tag->id }})"
              as="button"
              :color="$isActive ? 'lime' : 'zinc'"
              icon:variant="micro"
              size="sm"
              wire:loading.attr="disabled"
              wire:target="toggleTag({{ $tag->id }})"
            >
              <flux:icon.loader-circle variant="micro" class="animate-spin" wire:loading wire:target="toggleTag({{ $tag->id }})" />
              <flux:icon.check variant="micro" wire:loading.remove wire:target="toggleTag({{ $tag->id }})" @class(['hidden' => !$isActive]) />
              <flux:icon.plus variant="micro" wire:loading.remove wire:target="toggleTag({{ $tag->id }})" @class(['hidden' => $isActive]) />
              {{ $tag->name }}
            </flux:badge>
          @endforeach
        </div>
      @endif
    </div>

    <div class="flex items-center gap-1 shrink-0">
      @if ($recipe->hellofresh_url)
        <a
          href="{{ $recipe->hellofresh_url }}"
          target="_blank"
          rel="noopener noreferrer"
          class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
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
          class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
          title="{{ __('View PDF') }}"
        >
          <flux:icon.file-text variant="mini" />
        </a>
      @endif
      <livewire:web.recipes.add-to-list-button :recipe-id="$recipe->id" :key="'list-list-'.$recipe->id" />
      <button
        type="button"
        x-data
        x-on:click.prevent.stop="$store.shoppingList?.toggle({{ $recipe->id }})"
        class="rounded-full p-2 transition-colors bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
        x-bind:class="$store.shoppingList?.has({{ $recipe->id }}) && 'bg-green-500! text-white! hover:bg-green-600!'"
        x-bind:title="$store.shoppingList?.has({{ $recipe->id }}) ? '{{ __('Remove from shopping list') }}' : '{{ __('Add to shopping list') }}'"
      >
        <flux:icon.shopping-basket variant="mini" />
      </button>
    </div>
  </flux:card>
@endif
