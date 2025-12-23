<div>
    <flux:modal name="mini-cart-modal" class="md:w-xl">
        <div class="space-y-section">
            <flux:heading size="lg">{{ __('Shopping List') }}</flux:heading>

            @if($this->recipes->isNotEmpty())
                <div class="max-h-80 overflow-y-auto -mx-2">
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach($this->recipes as $recipe)
                            <li wire:key="mini-cart-{{ $recipe->id }}" class="flex items-center justify-between gap-2 px-2 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 rounded">
                                <span class="truncate text-sm">{{ $recipe->name }}</span>
                                <button
                                    type="button"
                                    x-on:click="$store.shoppingList?.remove({{ $recipe->id }}); $wire.open($store.shoppingList?.items ?? [])"
                                    class="shrink-0 rounded p-1 text-zinc-400 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400"
                                    title="{{ __('Remove') }}"
                                >
                                    <flux:icon.x variant="micro" />
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <flux:text class="text-zinc-500">
                    {{ __('Your shopping list is empty') }}
                </flux:text>
            @endif

            <flux:button
                variant="primary"
                class="w-full"
                icon="clipboard-list"
                :href="$shoppingListUrl"
                wire:navigate
            >
                {{ __('View Shopping List') }}
            </flux:button>
        </div>
    </flux:modal>
</div>
