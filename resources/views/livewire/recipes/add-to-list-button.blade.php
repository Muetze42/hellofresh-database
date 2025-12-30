<div wire:key="add-to-list-{{ $recipeId }}">
  <button
    type="button"
    wire:click="openModal"
    @class([
        'rounded-full p-2 transition-colors',
        'bg-white/80 text-zinc-700 hover:bg-white dark:bg-zinc-800/80 dark:text-zinc-300 dark:hover:bg-zinc-800' => !$this->isInAnyList,
        'bg-blue-500 text-white hover:bg-blue-600' => $this->isInAnyList,
    ])
    title="{{ $this->isInAnyList ? __('In list') : __('Add to list') }}"
  >
    <flux:icon.list-plus variant="mini" />
  </button>

  @auth
    <flux:modal name="add-to-list-{{ $recipeId }}" class="max-w-sm flex flex-col gap-section" @close="closeModal">
      <flux:heading size="lg">{{ __('Add to List') }}</flux:heading>

      @if($isModalOpen)
        <flux:pillbox wire:model="selectedLists" variant="combobox" multiple :filter="false" :placeholder="__('Select lists...')">
          <x-slot name="input">
            <flux:pillbox.input wire:model.live="search" :placeholder="__('Search or create list...')" />
          </x-slot>

          @foreach($this->lists as $list)
            <flux:pillbox.option wire:key="list-{{ $list->id }}" :value="$list->id">
              {{ $list->name }}
              @if($list->user_id !== auth()->id())
                <span class="text-zinc-400 text-xs">({{ $list->user->name }})</span>
              @endif
            </flux:pillbox.option>
          @endforeach

          <flux:pillbox.option.create wire:click="createList" min-length="2">
            {{ __('Create New List') }}: "<span wire:text="search"></span>"
          </flux:pillbox.option.create>
        </flux:pillbox>

        <div class="flex justify-end pt-section">
          <flux:button wire:click="saveLists" variant="primary">{{ __('Save') }}</flux:button>
        </div>
      @endif
    </flux:modal>
  @endauth
</div>
