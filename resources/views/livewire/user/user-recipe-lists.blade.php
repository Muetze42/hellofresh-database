<flux:main container>
  @if ($viewingListId && $this->viewingList)
    {{-- Viewing a specific list --}}
    <div class="space-y-section">
      <div class="flex items-center gap-4">
        <flux:button wire:click="backToLists" variant="ghost" icon="arrow-left" size="sm">
          {{ __('Back to Lists') }}
        </flux:button>
        <flux:heading size="lg">{{ $this->viewingList->name }}</flux:heading>
      </div>

      @if ($this->viewingList->description)
        <flux:text>{{ $this->viewingList->description }}</flux:text>
      @endif

      @if ($this->viewingList->recipes->isEmpty())
        <flux:card class="text-center py-12">
          <flux:icon.list class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
          <flux:heading size="lg" class="mt-4">{{ __('This list is empty') }}</flux:heading>
          <flux:text class="mt-2">{{ __('Add recipes to this list from the recipe pages.') }}</flux:text>
        </flux:card>
      @else
        <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          @foreach ($this->viewingList->recipes as $recipe)
            <flux:card wire:key="list-recipe-{{ $recipe->id }}" class="overflow-hidden">
              <div class="relative">
                @if ($recipe->card_image_url)
                  <img
                    src="{{ $recipe->card_image_url }}"
                    alt="{{ $recipe->name }}"
                    class="aspect-video w-full object-cover"
                  >
                @endif

                <div class="absolute top-2 right-2 flex gap-ui">
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

                  <button
                    type="button"
                    x-on:click.stop="$dispatch('confirm-action', {
                                        title: '{{ __('Remove from list') }}',
                                        message: '{{ __('Remove this recipe from the list?') }}',
                                        confirmText: '{{ __('Remove') }}',
                                        onConfirm: () => $wire.removeRecipeFromList({{ $recipe->id }})
                                    })"
                    class="rounded-full p-2 bg-red-500 text-white hover:bg-red-600 transition-colors"
                    title="{{ __('Remove from list') }}"
                  >
                    <flux:icon.x variant="mini" />
                  </button>
                </div>
              </div>

              <div class="p-4">
                <flux:heading size="lg" class="line-clamp-1">
                  <flux:link :href="localized_route('localized.recipes.show', ['recipe' => $recipe])">{{ $recipe->name }}</flux:link>
                </flux:heading>

                @if ($recipe->headline)
                  <flux:text class="mt-1 line-clamp-2">{{ $recipe->headline }}</flux:text>
                @endif
              </div>
            </flux:card>
          @endforeach
        </div>
      @endif
    </div>
  @else
    {{-- List overview --}}
    <div class="space-y-section">
      <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('My Lists') }}</flux:heading>
        <flux:modal.trigger name="create-list">
          <flux:button variant="primary" icon="plus" size="sm">
            {{ __('New List') }}
          </flux:button>
        </flux:modal.trigger>
      </div>

      @if ($this->recipeLists->isEmpty())
        <flux:card class="text-center py-12">
          <flux:icon.list class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
          <flux:heading size="lg" class="mt-4">{{ __('No lists yet') }}</flux:heading>
          <flux:text class="mt-2">{{ __('Create lists to organize your recipes.') }}</flux:text>
          <flux:modal.trigger name="create-list">
            <flux:button variant="primary" class="mt-4">
              {{ __('Create First List') }}
            </flux:button>
          </flux:modal.trigger>
        </flux:card>
      @else
        <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($this->recipeLists as $list)
            <flux:card wire:key="list-{{ $list->id }}" class="cursor-pointer hover:shadow-lg transition-shadow" wire:click="viewList({{ $list->id }})">
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <flux:heading size="lg" class="truncate">{{ $list->name }}</flux:heading>
                  @if ($list->description)
                    <flux:text class="mt-1 line-clamp-2">{{ $list->description }}</flux:text>
                  @endif
                  <flux:badge class="mt-2" size="sm">
                    {{ trans_choice(':count Recipe|:count Recipes', $list->recipes_count, ['count' => $list->recipes_count]) }}
                  </flux:badge>
                </div>

                <flux:dropdown>
                  <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" x-on:click.stop />

                  <flux:menu>
                    <flux:menu.item icon="pencil" wire:click.stop="startEditing({{ $list->id }})">
                      {{ __('Edit') }}
                    </flux:menu.item>
                    <flux:menu.item
                      icon="trash"
                      variant="danger"
                      x-on:click.stop="$dispatch('confirm-action', {
                                            title: '{{ __('Delete List') }}',
                                            message: '{{ __('Delete this list and all its recipes?') }}',
                                            confirmText: '{{ __('Delete') }}',
                                            onConfirm: () => $wire.deleteList({{ $list->id }})
                                        })"
                    >
                      {{ __('Delete') }}
                    </flux:menu.item>
                  </flux:menu>
                </flux:dropdown>
              </div>
            </flux:card>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Create List Modal --}}
    <flux:modal name="create-list" class="max-w-md space-y-section">
      <flux:heading size="lg">{{ __('Create New List') }}</flux:heading>

      <form wire:submit="createList" class="space-y-section">
        <flux:input
          wire:model="newListName"
          :label="__('List Name')"
          :placeholder="__('My favorite weeknight dinners')"
          required
        />

        <flux:textarea
          wire:model="newListDescription"
          :label="__('Description')"
          :placeholder="__('Optional description for your list')"
          rows="3"
        />

        <div class="flex justify-end gap-ui">
          <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="primary">{{ __('Create List') }}</flux:button>
        </div>
      </form>
    </flux:modal>

    {{-- Edit List Modal --}}
    <flux:modal name="edit-list" class="max-w-md space-y-section">
      <flux:heading size="lg">{{ __('Edit List') }}</flux:heading>

      <form wire:submit="updateList" class="space-y-section">
        <flux:input
          wire:model="editListName"
          :label="__('List Name')"
          required
        />

        <flux:textarea
          wire:model="editListDescription"
          :label="__('Description')"
          rows="3"
        />

        <div class="flex justify-end gap-ui">
          <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
        </div>
      </form>
    </flux:modal>
  @endif
</flux:main>
