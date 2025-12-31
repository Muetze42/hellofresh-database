<flux:main container class="space-y-section">
  @if ($this->shoppingLists->isEmpty())
    <flux:card class="text-center py-12">
      <flux:icon.shopping-basket class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
      <flux:heading size="lg" class="mt-4">{{ __('No saved shopping lists') }}</flux:heading>
      <flux:text class="mt-2">{{ __('Save your shopping lists from the shopping list page to access them later.') }}</flux:text>
      <flux:button :href="localized_route('localized.shopping-list.index')" variant="primary" class="mt-4">
        {{ __('Go to Shopping List') }}
      </flux:button>
    </flux:card>
  @else
    @foreach ($this->shoppingLists as $list)
      <flux:card wire:key="shopping-list-{{ $list->id }}">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <flux:heading size="lg">{{ $list->name }}</flux:heading>
            <flux:text class="mt-1">
              {{ trans_choice(':count Recipe|:count Recipes', count($list->items), ['count' => count($list->items)]) }}
              &middot;
              {{ __('Saved :date', ['date' => $list->created_at->diffForHumans()]) }}
            </flux:text>
          </div>

          <div class="flex items-center gap-ui shrink-0">
            <flux:button
              wire:click="loadList({{ json_encode($list->items) }})"
              variant="primary"
              size="sm"
              icon="download"
            >
              {{ __('Load') }}
            </flux:button>

            <flux:button
              wire:click="deleteList({{ $list->id }})"
              wire:confirm="{{ __('Delete this shopping list?') }}"
              variant="danger"
              size="sm"
              icon="trash"
            >
              {{ __('Delete') }}
            </flux:button>
          </div>
        </div>
      </flux:card>
    @endforeach
  @endif
</flux:main>
